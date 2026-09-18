<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
  events: {
    type: Array,
    default: () => []
  }
});

// Timezone handling
const detectedTimezone = Intl.DateTimeFormat().resolvedOptions().timeZone || 'UTC';
const timezones = [
  { name: 'Local (' + detectedTimezone + ')', value: detectedTimezone },
  { name: 'UTC / GMT', value: 'UTC' },
  { name: 'US Eastern (EST/EDT)', value: 'America/New_York' },
  { name: 'London (GMT/BST)', value: 'Europe/London' },
  { name: 'Tokyo (JST)', value: 'Asia/Tokyo' },
  { name: 'Sydney (AEST/AEDT)', value: 'Australia/Sydney' }
];
const selectedTimezone = ref(detectedTimezone);

// Time format (12h or 24h)
const timeFormat24 = ref(false);

// Filter states (Default all selected)
const availableCurrencies = ['USD', 'EUR', 'GBP', 'AUD', 'CAD', 'CHF', 'JPY', 'NZD', 'CNY'];
const selectedCurrencies = ref([...availableCurrencies]);
const selectedImpacts = ref(['High', 'Medium', 'Low', 'Holiday']);

// Demo Mode for simulated actual values
const demoMode = ref(true);

// Expandable rows
const expandedEvents = ref(new Set());

// Currency metadata for flags and names
const currencyMeta = {
  USD: { name: 'US Dollar', flag: '🇺🇸' },
  EUR: { name: 'Euro', flag: '🇪🇺' },
  GBP: { name: 'British Pound', flag: '🇬🇧' },
  JPY: { name: 'Japanese Yen', flag: '🇯🇵' },
  AUD: { name: 'Australian Dollar', flag: '🇦🇺' },
  CAD: { name: 'Canadian Dollar', flag: '🇨🇦' },
  CHF: { name: 'Swiss Franc', flag: '🇨🇭' },
  NZD: { name: 'New Zealand Dollar', flag: '🇳🇿' },
  CNY: { name: 'Chinese Yuan', flag: '🇨🇳' },
  All: { name: 'Global', flag: '🌐' }
};

// ----------------------------------------------------
// FOREX FACTORY NAVIGATION STATE & CALCULATIONS
// ----------------------------------------------------
const viewMode = ref('week'); // 'day' | 'week' | 'month'
const selectedDate = ref(new Date());
const currentMonthYear = ref(new Date(selectedDate.value.getFullYear(), selectedDate.value.getMonth(), 1));

// Calculate start & end range bounds
const activeRange = computed(() => {
  const anchor = new Date(selectedDate.value);
  
  if (viewMode.value === 'day') {
    const start = new Date(anchor);
    start.setHours(0, 0, 0, 0);
    const end = new Date(anchor);
    end.setHours(23, 59, 59, 999);
    return { start, end };
  } else if (viewMode.value === 'week') {
    const day = anchor.getDay();
    const diff = anchor.getDate() - day; // Adjust to Sunday
    const start = new Date(anchor.setDate(diff));
    start.setHours(0, 0, 0, 0);
    
    const end = new Date(start);
    end.setDate(start.getDate() + 6);
    end.setHours(23, 59, 59, 999);
    return { start, end };
  } else {
    // month view
    const start = new Date(anchor.getFullYear(), anchor.getMonth(), 1, 0, 0, 0, 0);
    const end = new Date(anchor.getFullYear(), anchor.getMonth() + 1, 0, 23, 59, 59, 999);
    return { start, end };
  }
});

// Title label for the current range
const activeRangeLabel = computed(() => {
  const start = activeRange.value.start;
  const end = activeRange.value.end;
  
  if (viewMode.value === 'day') {
    return start.toLocaleDateString('en-US', { weekday: 'short', month: 'short', day: 'numeric', year: 'numeric' });
  } else if (viewMode.value === 'week') {
    const startStr = start.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
    const endStr = end.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    return `${startStr} - ${endStr}`;
  } else {
    return start.toLocaleDateString('en-US', { month: 'long', year: 'numeric' });
  }
});

// Navigate page previous/next period
const prevPeriod = () => {
  const current = new Date(selectedDate.value);
  if (viewMode.value === 'day') {
    current.setDate(current.getDate() - 1);
  } else if (viewMode.value === 'week') {
    current.setDate(current.getDate() - 7);
  } else {
    current.setMonth(current.getMonth() - 1);
  }
  selectedDate.value = current;
  currentMonthYear.value = new Date(current.getFullYear(), current.getMonth(), 1);
};

const nextPeriod = () => {
  const current = new Date(selectedDate.value);
  if (viewMode.value === 'day') {
    current.setDate(current.getDate() + 1);
  } else if (viewMode.value === 'week') {
    current.setDate(current.getDate() + 7);
  } else {
    current.setMonth(current.getMonth() + 1);
  }
  selectedDate.value = current;
  currentMonthYear.value = new Date(current.getFullYear(), current.getMonth(), 1);
};

// Mini calendar computations
const miniCalendarDays = computed(() => {
  const year = currentMonthYear.value.getFullYear();
  const month = currentMonthYear.value.getMonth();
  
  const firstDay = new Date(year, month, 1);
  const startDay = firstDay.getDay();
  
  const lastDay = new Date(year, month + 1, 0);
  const totalDays = lastDay.getDate();
  
  const days = [];
  
  // Previous month trailing days
  const prevMonthLast = new Date(year, month, 0).getDate();
  for (let i = startDay - 1; i >= 0; i--) {
    days.push({
      date: new Date(year, month - 1, prevMonthLast - i),
      isCurrentMonth: false
    });
  }
  
  // Current month days
  for (let i = 1; i <= totalDays; i++) {
    days.push({
      date: new Date(year, month, i),
      isCurrentMonth: true
    });
  }
  
  // Next month leading days to complete grid of 42 cells (6 rows)
  const remaining = 42 - days.length;
  for (let i = 1; i <= remaining; i++) {
    days.push({
      date: new Date(year, month + 1, i),
      isCurrentMonth: false
    });
  }
  
  return days;
});

const isDaySelected = (date) => {
  return viewMode.value === 'day' && date.toDateString() === selectedDate.value.toDateString();
};

const isWeekSelected = (date) => {
  if (viewMode.value !== 'week') return false;
  // Get Sunday of selectedDate and current cell date
  const getSunday = (d) => {
    const temp = new Date(d);
    const day = temp.getDay();
    const diff = temp.getDate() - day;
    const sun = new Date(temp.setDate(diff));
    sun.setHours(0,0,0,0);
    return sun;
  };
  return getSunday(date).toDateString() === getSunday(selectedDate.value).toDateString();
};

const isTodayDate = (date) => {
  return date.toDateString() === new Date().toDateString();
};

const selectDay = (date) => {
  selectedDate.value = date;
  viewMode.value = 'day';
};

const selectWeekFromRow = (rowIdx) => {
  const sunday = miniCalendarDays.value[rowIdx * 7].date;
  selectedDate.value = sunday;
  viewMode.value = 'week';
};

const prevMiniMonth = () => {
  const current = currentMonthYear.value;
  currentMonthYear.value = new Date(current.getFullYear(), current.getMonth() - 1, 1);
};

const nextMiniMonth = () => {
  const current = currentMonthYear.value;
  currentMonthYear.value = new Date(current.getFullYear(), current.getMonth() + 1, 1);
};

// Quick navigation buttons
const selectToday = () => {
  const d = new Date();
  selectedDate.value = d;
  viewMode.value = 'day';
  currentMonthYear.value = new Date(d.getFullYear(), d.getMonth(), 1);
};

const selectTomorrow = () => {
  const d = new Date();
  d.setDate(d.getDate() + 1);
  selectedDate.value = d;
  viewMode.value = 'day';
  currentMonthYear.value = new Date(d.getFullYear(), d.getMonth(), 1);
};

const selectYesterday = () => {
  const d = new Date();
  d.setDate(d.getDate() - 1);
  selectedDate.value = d;
  viewMode.value = 'day';
  currentMonthYear.value = new Date(d.getFullYear(), d.getMonth(), 1);
};

const selectThisWeek = () => {
  const d = new Date();
  selectedDate.value = d;
  viewMode.value = 'week';
  currentMonthYear.value = new Date(d.getFullYear(), d.getMonth(), 1);
};

const selectNextWeek = () => {
  const d = new Date();
  d.setDate(d.getDate() + 7);
  selectedDate.value = d;
  viewMode.value = 'week';
  currentMonthYear.value = new Date(d.getFullYear(), d.getMonth(), 1);
};

const selectLastWeek = () => {
  const d = new Date();
  d.setDate(d.getDate() - 7);
  selectedDate.value = d;
  viewMode.value = 'week';
  currentMonthYear.value = new Date(d.getFullYear(), d.getMonth(), 1);
};

const selectThisMonth = () => {
  const d = new Date();
  selectedDate.value = d;
  viewMode.value = 'month';
  currentMonthYear.value = new Date(d.getFullYear(), d.getMonth(), 1);
};

const selectNextMonth = () => {
  const d = new Date();
  d.setMonth(d.getMonth() + 1);
  selectedDate.value = d;
  viewMode.value = 'month';
  currentMonthYear.value = new Date(d.getFullYear(), d.getMonth(), 1);
};

const selectLastMonth = () => {
  const d = new Date();
  d.setMonth(d.getMonth() - 1);
  selectedDate.value = d;
  viewMode.value = 'month';
  currentMonthYear.value = new Date(d.getFullYear(), d.getMonth(), 1);
};

const selectUpNext = () => {
  const now = new Date();
  const upNext = allEvents.value.find(e => new Date(e.date) > now);
  if (upNext) {
    const d = new Date(upNext.date);
    selectedDate.value = d;
    viewMode.value = 'day';
    currentMonthYear.value = new Date(d.getFullYear(), d.getMonth(), 1);
    expandedEvents.value.add(getEventId(upNext));
  } else {
    // Fallback to today
    selectToday();
  }
};

// ----------------------------------------------------
// DETERMINISTIC MOCK DATA GENERATOR
// ----------------------------------------------------
const stringHash = (str) => {
  let hash = 0;
  for (let i = 0; i < str.length; i++) {
    hash = str.charCodeAt(i) + ((hash << 5) - hash);
  }
  return Math.abs(hash);
};

const getEventId = (event) => {
  return `${event.title}-${event.country}-${event.date}`;
};

const currentWeekBounds = computed(() => {
  const now = new Date();
  const day = now.getDay();
  const diff = now.getDate() - day; // Sunday of current week
  const start = new Date(now.setDate(diff));
  start.setHours(0, 0, 0, 0);
  
  const end = new Date(start);
  end.setDate(start.getDate() + 6);
  end.setHours(23, 59, 59, 999);
  
  return { start, end };
});

const generateMockEventsForDate = (date) => {
  const dayOfWeek = date.getDay();
  if (dayOfWeek === 0 || dayOfWeek === 6) return []; // Weekends are generally empty
  
  const dateStr = date.toISOString().split('T')[0];
  const daySeed = stringHash(dateStr);
  
  // 2 to 4 events per weekday
  const count = 2 + (daySeed % 3);
  
  const templates = [
    { title: "Core CPI m/m", country: "USD", impact: "High", type: "pct", base: 0.2, spread: 0.3, hour: 8, minute: 30 },
    { title: "Unemployment Rate", country: "USD", impact: "High", type: "pct", base: 3.9, spread: 0.5, hour: 8, minute: 30 },
    { title: "ADP Non-Farm Employment Change", country: "USD", impact: "Medium", type: "k", base: 145, spread: 60, hour: 8, minute: 15 },
    { title: "FOMC Member Speaks", country: "USD", impact: "Low", type: "none", base: 0, spread: 0, hour: 13, minute: 0 },
    { title: "ISM Services PMI", country: "USD", impact: "High", type: "index", base: 51.5, spread: 4.5, hour: 10, minute: 0 },
    { title: "CB Consumer Confidence", country: "USD", impact: "Medium", type: "index", base: 101.8, spread: 10.0, hour: 10, minute: 0 },
    { title: "German Ifo Business Climate", country: "EUR", impact: "Medium", type: "index", base: 85.5, spread: 6.0, hour: 4, minute: 0 },
    { title: "CPI Flash Estimate y/y", country: "EUR", impact: "High", type: "pct", base: 2.2, spread: 0.8, hour: 5, minute: 0 },
    { title: "ECB Interest Rate Decision", country: "EUR", impact: "High", type: "pct", base: 3.75, spread: 0.25, hour: 8, minute: 15 },
    { title: "CPI y/y", country: "GBP", impact: "High", type: "pct", base: 2.3, spread: 0.9, hour: 2, minute: 0 },
    { title: "Official Bank Rate", country: "GBP", impact: "High", type: "pct", base: 4.5, spread: 0.25, hour: 7, minute: 0 },
    { title: "Employment Change", country: "AUD", impact: "High", type: "k", base: 25, spread: 20, hour: 21, minute: 30 },
    { title: "RBA Rate Decision", country: "AUD", impact: "High", type: "pct", base: 4.1, spread: 0.25, hour: 22, minute: 30 },
    { title: "BOC Rate Statement", country: "CAD", impact: "High", type: "pct", base: 4.0, spread: 0.25, hour: 9, minute: 45 },
    { title: "Employment Change", country: "CAD", impact: "High", type: "k", base: 15, spread: 15, hour: 8, minute: 30 },
    { title: "BOJ Policy Rate", country: "JPY", impact: "High", type: "pct", base: 0.25, spread: 0.1, hour: 23, minute: 0 },
    { title: "SNB Policy Rate", country: "CHF", impact: "High", type: "pct", base: 1.0, spread: 0.25, hour: 3, minute: 30 },
    { title: "Official Cash Rate", country: "NZD", impact: "High", type: "pct", base: 4.75, spread: 0.25, hour: 21, minute: 0 }
  ];
  
  const chosen = [];
  for (let i = 0; i < count; i++) {
    const idx = (daySeed + i * 11) % templates.length;
    if (!chosen.includes(idx)) {
      chosen.push(idx);
    }
  }
  
  return chosen.map(idx => {
    const t = templates[idx];
    const eventSeed = stringHash(t.title + dateStr);
    
    let prev = '-';
    let forecast = '-';
    let actual = '';
    
    if (t.type === 'pct') {
      const p = t.base + ((eventSeed % 21) - 10) / 100;
      prev = p.toFixed(1) + '%';
      const f = p + ((eventSeed % 7) - 3) / 100;
      forecast = f.toFixed(1) + '%';
      
      if (date < new Date()) {
        const a = f + ((eventSeed % 5) - 2) / 100;
        actual = a.toFixed(1) + '%';
      }
    } else if (t.type === 'index') {
      const p = t.base + ((eventSeed % 41) - 20) / 10;
      prev = p.toFixed(1);
      const f = p + ((eventSeed % 11) - 5) / 10;
      forecast = f.toFixed(1);
      
      if (date < new Date()) {
        const a = f + ((eventSeed % 7) - 3) / 10;
        actual = a.toFixed(1);
      }
    } else if (t.type === 'k') {
      const p = Math.round(t.base + ((eventSeed % 51) - 25));
      prev = p + 'K';
      const f = Math.round(p + ((eventSeed % 15) - 7));
      forecast = f + 'K';
      
      if (date < new Date()) {
        const a = Math.round(f + ((eventSeed % 9) - 4));
        actual = a + 'K';
      }
    }
    
    // Set time in UTC
    const evDate = new Date(date);
    evDate.setUTCHours(t.hour, t.minute, 0, 0);
    
    return {
      title: t.title,
      country: t.country,
      date: evDate.toISOString(),
      impact: t.impact,
      forecast: forecast,
      previous: prev,
      actual: actual || null
    };
  });
};

// Combine API and generated mock data
const allEvents = computed(() => {
  const list = [];
  const start = new Date(activeRange.value.start);
  const end = new Date(activeRange.value.end);
  
  const curr = new Date(start);
  while (curr <= end) {
    const isAPIWeek = curr >= currentWeekBounds.value.start && curr <= currentWeekBounds.value.end;
    
    if (isAPIWeek) {
      // Filter props.events for matches on this day
      const dayStr = curr.toDateString();
      const matched = props.events.filter(e => new Date(e.date).toDateString() === dayStr);
      
      matched.forEach(e => {
        list.push({
          title: e.title,
          country: e.country || 'USD',
          date: e.date,
          impact: e.impact || 'Low',
          forecast: e.forecast || '',
          previous: e.previous || '',
          actual: e.actual || null
        });
      });
    } else {
      // Generate deterministic mock events
      generateMockEventsForDate(curr).forEach(e => {
        list.push(e);
      });
    }
    
    curr.setDate(curr.getDate() + 1);
  }
  
  return list;
});

// Filter & Group computations
const filteredEvents = computed(() => {
  return allEvents.value.filter(event => {
    // Check currency match
    const isGlobal = event.country === 'All';
    const matchesCurrency = isGlobal || selectedCurrencies.value.includes(event.country);
    
    // Check impact match
    const matchesImpact = selectedImpacts.value.includes(event.impact);
    
    return matchesCurrency && matchesImpact;
  });
});

const groupedEvents = computed(() => {
  const groups = {};

  filteredEvents.value.forEach(event => {
    const dateLabel = formatEventDate(event.date, selectedTimezone.value);
    if (!groups[dateLabel]) {
      groups[dateLabel] = [];
    }
    groups[dateLabel].push(event);
  });

  // Sort day keys chronologically
  const sortedKeys = Object.keys(groups).sort((a, b) => {
    const dateA = new Date(groups[a][0].date);
    const dateB = new Date(groups[b][0].date);
    return dateA - dateB;
  });

  return sortedKeys.map(key => {
    const sortedEvents = groups[key].sort((a, b) => new Date(a.date) - new Date(b.date));
    return {
      dateLabel: key,
      events: sortedEvents
    };
  });
});

// Original helpers and details
const toggleEvent = (eventId) => {
  if (expandedEvents.value.has(eventId)) {
    expandedEvents.value.delete(eventId);
  } else {
    expandedEvents.value.add(eventId);
  }
};

const toggleAllCurrencies = () => {
  if (selectedCurrencies.value.length === availableCurrencies.length) {
    selectedCurrencies.value = [];
  } else {
    selectedCurrencies.value = [...availableCurrencies];
  }
};

const toggleAllImpacts = () => {
  if (selectedImpacts.value.length === 4) {
    selectedImpacts.value = [];
  } else {
    selectedImpacts.value = ['High', 'Medium', 'Low', 'Holiday'];
  }
};

const formatEventTime = (isoString, tz, format24) => {
  try {
    const date = new Date(isoString);
    return date.toLocaleTimeString('en-US', {
      timeZone: tz,
      hour: 'numeric',
      minute: '2-digit',
      hour12: !format24
    });
  } catch (e) {
    return 'Tentative';
  }
};

const formatEventDate = (isoString, tz) => {
  try {
    const date = new Date(isoString);
    return date.toLocaleDateString('en-US', {
      timeZone: tz,
      weekday: 'long',
      month: 'short',
      day: 'numeric',
      year: 'numeric'
    });
  } catch (e) {
    return 'Unknown Date';
  }
};

const getEventActual = (event) => {
  if (event.actual) return { value: event.actual, color: 'text-slate-150' };

  const eventDate = new Date(event.date);
  const now = new Date();
  if (!demoMode.value || eventDate >= now) {
    return { value: '-', color: 'text-slate-500' };
  }

  const forecastStr = event.forecast;
  if (!forecastStr) {
    return { value: '-', color: 'text-slate-500' };
  }

  const eventId = getEventId(event);
  let hash = 0;
  for (let i = 0; i < eventId.length; i++) {
    hash = eventId.charCodeAt(i) + ((hash << 5) - hash);
  }
  const random = (hash % 100) / 100;

  const numericMatch = forecastStr.match(/(-?\d+(\.\d+)?)/);
  if (!numericMatch) {
    return { value: forecastStr, color: 'text-slate-100 font-medium' };
  }

  const forecastVal = parseFloat(numericMatch[1]);
  const isPercentage = forecastStr.includes('%');
  const isIndex = !isPercentage && !forecastStr.toLowerCase().includes('k') && !forecastStr.toLowerCase().includes('m') && !forecastStr.toLowerCase().includes('b');
  const isKMB = forecastStr.toLowerCase().includes('k') || forecastStr.toLowerCase().includes('m') || forecastStr.toLowerCase().includes('b');

  let delta = 0;
  if (isPercentage) {
    delta = random > 0 ? 0.1 : (random < -0.4 ? -0.1 : 0);
  } else if (isIndex) {
    delta = Math.round(random * 12) / 10;
  } else if (isKMB) {
    delta = Math.round(random * 8);
  }

  const actualVal = parseFloat((forecastVal + delta).toFixed(2));
  const unit = forecastStr.replace(/(-?\d+(\.\d+)?)/, '');
  const actualStr = actualVal + unit;

  let higherIsBetter = true;
  const lowerIsBetterKeywords = ['unemployment', 'claims', 'cpi', 'ppi', 'inflation', 'deficit'];
  const titleLower = event.title.toLowerCase();
  for (const keyword of lowerIsBetterKeywords) {
    if (titleLower.includes(keyword)) {
      higherIsBetter = false;
      break;
    }
  }

  let color = 'text-slate-200';
  if (delta !== 0) {
    if (higherIsBetter) {
      color = delta > 0 ? 'text-emerald-400 font-bold' : 'text-rose-400 font-bold';
    } else {
      color = delta < 0 ? 'text-emerald-400 font-bold' : 'text-rose-400 font-bold';
    }
  }

  return { value: actualStr, color };
};

const getEventDetails = (event) => {
  const titleLower = event.title.toLowerCase();
  let details = {
    frequency: 'Monthly',
    whyTradersCare: 'Measures changes in macroeconomic trends. Traders analyze this data to project central bank interest rate policy adjustments.',
    source: 'National Statistics Agency',
    url: event.url || 'https://www.forexfactory.com/calendar'
  };

  if (titleLower.includes('cpi') || titleLower.includes('inflation') || titleLower.includes('price index')) {
    details.whyTradersCare = 'Consumer prices account for the majority of overall inflation. Inflation is critical to currency valuation because rising prices lead the central bank to raise interest rates to contain inflation.';
    details.frequency = 'Monthly';
    details.source = 'Bureau of Labor / National Statistics';
  } else if (titleLower.includes('interest rate') || titleLower.includes('rate decision') || titleLower.includes('monetary policy') || titleLower.includes('rba') || titleLower.includes('boj') || titleLower.includes('ecb')) {
    details.whyTradersCare = 'Short term interest rates are the single most important factor in currency valuation. Traders monitor inflation and employment data to forecast future interest rate adjustments.';
    details.frequency = '8-12 times per year';
    details.source = 'Federal Reserve / ECB / central bank boards';
  } else if (titleLower.includes('retail sales')) {
    details.whyTradersCare = 'It is the primary gauge of consumer spending, which accounts for the vast majority of overall economic activity. Strong retail sales show a robust economy and boost the currency.';
    details.frequency = 'Monthly';
    details.source = 'Census Bureau / Statistics Dept';
  } else if (titleLower.includes('gdp') || titleLower.includes('gross domestic product')) {
    details.whyTradersCare = 'Gross Domestic Product (GDP) is the broadest measure of economic activity and the primary indicator of the economy\'s overall growth rate and health.';
    details.frequency = 'Quarterly';
    details.source = 'Bureau of Economic Analysis / Office for National Statistics';
  } else if (titleLower.includes('unemployment') || titleLower.includes('payrolls') || titleLower.includes('employment change') || titleLower.includes('jobs')) {
    details.whyTradersCare = 'Job creation is an important leading indicator of consumer spending. Higher employment stimulates overall growth and supports hawkish interest rate policies.';
    details.frequency = 'Monthly';
    details.source = 'Bureau of Labor Statistics / Department of Employment';
  } else if (titleLower.includes('pmi') || titleLower.includes('purchasing managers')) {
    details.whyTradersCare = 'PMI is a leading indicator of economic health. Purchasing managers hold the most current and relevant insight into the company\'s view of demand, input costs, and hiring.';
    details.frequency = 'Monthly';
    details.source = 'S&P Global / Institute for Supply Management (ISM)';
  } else if (titleLower.includes('confidence') || titleLower.includes('sentiment')) {
    details.whyTradersCare = 'Consumer confidence is a leading indicator of consumer spending. Consumers spend more when they feel optimistic about their job security and future income prospects.';
    details.frequency = 'Monthly';
    details.source = 'The Conference Board / University of Michigan';
  }

  return details;
};
</script>

<template>
  <Head title="Economic Calendar" />
  <AuthenticatedLayout>
    <template #header>
      <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
        <div>
          <h2 class="font-bold text-2xl text-slate-100 leading-tight">Economic Calendar</h2>
          <p class="text-sm text-slate-400 mt-1">Real-time market-moving economic events powered by Forex Factory CDN.</p>
        </div>
        <div class="flex items-center space-x-3">
          <label class="inline-flex items-center cursor-pointer bg-slate-800 border border-slate-700 rounded-lg px-3 py-1.5 text-xs text-slate-300">
            <input type="checkbox" v-model="demoMode" class="sr-only peer">
            <span class="mr-2">Demo Live Values</span>
            <div class="relative w-8 h-4 bg-slate-600 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-emerald-500"></div>
          </label>
        </div>
      </div>
    </template>

    <div class="py-6 min-h-screen bg-slate-950">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
          
          <!-- LEFT SIDEBAR: NAVIGATION, FILTERS & SETTINGS -->
          <div class="lg:col-span-1 space-y-6">
            
            <!-- Mini Calendar Navigation Widget -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 shadow-lg shadow-black/30">
              <div class="flex justify-between items-center mb-4 pb-2 border-b border-slate-800">
                <button @click="prevMiniMonth" class="text-slate-400 hover:text-white p-1 hover:bg-slate-800 rounded transition">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <span class="text-xs font-bold text-slate-200 font-mono">
                  {{ currentMonthYear.toLocaleDateString('en-US', { month: 'short', year: 'numeric' }) }}
                </span>
                <button @click="nextMiniMonth" class="text-slate-400 hover:text-white p-1 hover:bg-slate-800 rounded transition">
                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </button>
              </div>

              <!-- Days Grid -->
              <div class="grid grid-cols-8 gap-1 text-center text-[10px] select-none">
                <!-- Arrow Header Col -->
                <div></div>
                <!-- Weekday Headers -->
                <div v-for="d in ['S', 'M', 'T', 'W', 'T', 'F', 'S']" :key="d" class="font-bold text-slate-400 py-1">{{ d }}</div>
                
                <!-- 6 Rows of Grid -->
                <template v-for="rowIdx in [0, 1, 2, 3, 4, 5]" :key="'row-'+rowIdx">
                  <!-- Row Selector Arrow -->
                  <button 
                    @click="selectWeekFromRow(rowIdx)" 
                    class="text-slate-650 hover:text-emerald-400 font-bold transition flex items-center justify-center py-1 font-mono"
                    title="Select whole week"
                  >
                    »
                  </button>
                  <!-- Days Cells -->
                  <button 
                    v-for="colIdx in [0, 1, 2, 3, 4, 5, 6]" 
                    :key="'day-'+rowIdx+'-'+colIdx"
                    @click="selectDay(miniCalendarDays[rowIdx * 7 + colIdx].date)"
                    class="py-1 rounded-md transition font-mono relative flex items-center justify-center font-bold"
                    :class="[
                      miniCalendarDays[rowIdx * 7 + colIdx].isCurrentMonth ? 'text-slate-200 hover:bg-slate-800' : 'text-slate-600 hover:bg-slate-800/40',
                      isDaySelected(miniCalendarDays[rowIdx * 7 + colIdx].date) ? 'bg-emerald-500 text-slate-950 font-extrabold hover:bg-emerald-400 shadow-lg shadow-emerald-500/20' : '',
                      isWeekSelected(miniCalendarDays[rowIdx * 7 + colIdx].date) && !isDaySelected(miniCalendarDays[rowIdx * 7 + colIdx].date) ? 'bg-emerald-500/10 text-emerald-300 border border-emerald-500/20' : '',
                      isTodayDate(miniCalendarDays[rowIdx * 7 + colIdx].date) && !isDaySelected(miniCalendarDays[rowIdx * 7 + colIdx].date) ? 'underline decoration-emerald-400 decoration-2 font-black' : ''
                    ]"
                  >
                    {{ miniCalendarDays[rowIdx * 7 + colIdx].date.getDate() }}
                  </button>
                </template>
              </div>

              <!-- Quick Links section below calendar -->
              <div class="grid grid-cols-2 gap-x-4 gap-y-2 text-xs font-semibold mt-4 pt-4 border-t border-slate-800 font-mono">
                <button @click="selectToday" class="text-slate-400 hover:text-emerald-400 text-left transition flex items-center">
                  <span class="w-1.5 h-1.5 rounded-full mr-2" :class="viewMode === 'day' && isTodayDate(selectedDate) ? 'bg-emerald-500' : 'bg-transparent'"></span>
                  Today
                </button>
                <button @click="selectTomorrow" class="text-slate-400 hover:text-emerald-400 text-left transition flex items-center">
                  <span class="w-1.5 h-1.5 rounded-full mr-2" :class="viewMode === 'day' && selectedDate.getDate() === new Date(new Date().setDate(new Date().getDate() + 1)).getDate() ? 'bg-emerald-500' : 'bg-transparent'"></span>
                  Tomorrow
                </button>
                <button @click="selectThisWeek" class="text-slate-400 hover:text-emerald-400 text-left transition flex items-center">
                  <span class="w-1.5 h-1.5 rounded-full mr-2" :class="viewMode === 'week' && isTodayDate(selectedDate) ? 'bg-emerald-500' : 'bg-transparent'"></span>
                  This Week
                </button>
                <button @click="selectNextWeek" class="text-slate-400 hover:text-emerald-400 text-left transition flex items-center">
                  <span class="w-1.5 h-1.5 rounded-full mr-2" :class="viewMode === 'week' && selectedDate.getDate() === new Date(new Date().setDate(new Date().getDate() + 7)).getDate() ? 'bg-emerald-500' : 'bg-transparent'"></span>
                  Next Week
                </button>
                <button @click="selectThisMonth" class="text-slate-400 hover:text-emerald-400 text-left transition flex items-center">
                  <span class="w-1.5 h-1.5 rounded-full mr-2" :class="viewMode === 'month' && selectedDate.getMonth() === new Date().getMonth() ? 'bg-emerald-500' : 'bg-transparent'"></span>
                  This Month
                </button>
                <button @click="selectNextMonth" class="text-slate-400 hover:text-emerald-400 text-left transition flex items-center">
                  <span class="w-1.5 h-1.5 rounded-full mr-2" :class="viewMode === 'month' && selectedDate.getMonth() === (new Date().getMonth() + 1) % 12 ? 'bg-emerald-500' : 'bg-transparent'"></span>
                  Next Month
                </button>
                <button @click="selectYesterday" class="text-slate-400 hover:text-emerald-400 text-left transition flex items-center">
                  <span class="w-1.5 h-1.5 rounded-full mr-2" :class="viewMode === 'day' && selectedDate.getDate() === new Date(new Date().setDate(new Date().getDate() - 1)).getDate() ? 'bg-emerald-500' : 'bg-transparent'"></span>
                  Yesterday
                </button>
                <button @click="selectUpNext" class="text-slate-400 hover:text-emerald-400 text-left transition flex items-center">
                  <span class="w-1.5 h-1.5 rounded-full bg-transparent mr-2"></span>
                  Up Next
                </button>
                <button @click="selectLastWeek" class="text-slate-400 hover:text-emerald-400 text-left transition flex items-center">
                  <span class="w-1.5 h-1.5 rounded-full mr-2" :class="viewMode === 'week' && selectedDate.getDate() === new Date(new Date().setDate(new Date().getDate() - 7)).getDate() ? 'bg-emerald-500' : 'bg-transparent'"></span>
                  Last Week
                </button>
                <button @click="selectLastMonth" class="text-slate-400 hover:text-emerald-400 text-left transition flex items-center">
                  <span class="w-1.5 h-1.5 rounded-full mr-2" :class="viewMode === 'month' && selectedDate.getMonth() === (new Date().getMonth() - 1 + 12) % 12 ? 'bg-emerald-500' : 'bg-transparent'"></span>
                  Last Month
                </button>
              </div>
            </div>

            <!-- Timezone Settings -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 shadow-lg shadow-black/30">
              <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-4 flex items-center">
                <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                Timezone Settings
              </h3>
              <div class="space-y-4">
                <div>
                  <label class="block text-xs text-slate-400 mb-1">Display Timezone</label>
                  <select 
                    v-model="selectedTimezone"
                    class="w-full bg-slate-950 border border-slate-800 rounded-lg text-slate-200 text-sm focus:border-emerald-500 focus:ring-emerald-500/10 transition py-2 px-3 animate-none"
                  >
                    <option v-for="tz in timezones" :key="tz.value" :value="tz.value">
                      {{ tz.name }}
                    </option>
                  </select>
                </div>
                
                <div class="flex items-center justify-between">
                  <span class="text-xs text-slate-400">24-Hour Format</span>
                  <button 
                    @click="timeFormat24 = !timeFormat24"
                    class="relative inline-flex h-5 w-9 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                    :class="timeFormat24 ? 'bg-emerald-500' : 'bg-slate-700'"
                  >
                    <span 
                      class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                      :class="timeFormat24 ? 'translate-x-4' : 'translate-x-0'"
                    />
                  </button>
                </div>
              </div>
            </div>

            <!-- Currency Filters -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 shadow-lg shadow-black/30">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center">
                  <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Currencies
                </h3>
                <button @click="toggleAllCurrencies" class="text-[10px] text-emerald-400 hover:underline">
                  {{ selectedCurrencies.length === availableCurrencies.length ? 'Deselect All' : 'Select All' }}
                </button>
              </div>
              
              <div class="grid grid-cols-2 gap-2.5">
                <label 
                  v-for="currency in availableCurrencies" 
                  :key="currency" 
                  class="flex items-center space-x-2 p-2 bg-slate-950 border rounded-lg cursor-pointer hover:border-slate-700 transition"
                  :class="selectedCurrencies.includes(currency) ? 'border-emerald-500/50 bg-emerald-950/10' : 'border-slate-800'"
                >
                  <input 
                    type="checkbox" 
                    :value="currency" 
                    v-model="selectedCurrencies"
                    class="rounded border-slate-800 text-emerald-500 focus:ring-emerald-500 bg-slate-900 w-3.5 h-3.5"
                  >
                  <span class="text-xs text-slate-300 font-medium">
                    <span class="mr-1">{{ currencyMeta[currency]?.flag }}</span>
                    {{ currency }}
                  </span>
                </label>
              </div>
            </div>

            <!-- Impact Filters -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl p-5 shadow-lg shadow-black/30">
              <div class="flex justify-between items-center mb-4">
                <h3 class="text-xs font-semibold text-slate-400 uppercase tracking-wider flex items-center">
                  <svg class="w-4 h-4 mr-2 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                  Impact / Volatility
                </h3>
                <button @click="toggleAllImpacts" class="text-[10px] text-emerald-400 hover:underline">
                  {{ selectedImpacts.length === 4 ? 'Deselect All' : 'Select All' }}
                </button>
              </div>
              
              <div class="space-y-2">
                <!-- High Impact -->
                <label 
                  class="flex items-center justify-between p-2.5 bg-slate-950 border rounded-lg cursor-pointer hover:border-slate-700 transition"
                  :class="selectedImpacts.includes('High') ? 'border-red-500/50 bg-red-950/5' : 'border-slate-800'"
                >
                  <div class="flex items-center space-x-2">
                    <input type="checkbox" value="High" v-model="selectedImpacts" class="rounded border-slate-800 text-red-500 focus:ring-red-500 bg-slate-900 w-3.5 h-3.5">
                    <span class="text-xs font-semibold text-red-500">High Impact</span>
                  </div>
                  <span class="w-2.5 h-2.5 rounded bg-red-500"></span>
                </label>

                <!-- Medium Impact -->
                <label 
                  class="flex items-center justify-between p-2.5 bg-slate-950 border rounded-lg cursor-pointer hover:border-slate-700 transition"
                  :class="selectedImpacts.includes('Medium') ? 'border-orange-500/50 bg-orange-950/5' : 'border-slate-800'"
                >
                  <div class="flex items-center space-x-2">
                    <input type="checkbox" value="Medium" v-model="selectedImpacts" class="rounded border-slate-800 text-orange-500 focus:ring-orange-500 bg-slate-900 w-3.5 h-3.5">
                    <span class="text-xs font-semibold text-orange-500">Medium Impact</span>
                  </div>
                  <span class="w-2.5 h-2.5 rounded bg-orange-500"></span>
                </label>

                <!-- Low Impact -->
                <label 
                  class="flex items-center justify-between p-2.5 bg-slate-950 border rounded-lg cursor-pointer hover:border-slate-700 transition"
                  :class="selectedImpacts.includes('Low') ? 'border-yellow-500/50 bg-yellow-950/5' : 'border-slate-800'"
                >
                  <div class="flex items-center space-x-2">
                    <input type="checkbox" value="Low" v-model="selectedImpacts" class="rounded border-slate-800 text-yellow-500 focus:ring-yellow-500 bg-slate-900 w-3.5 h-3.5">
                    <span class="text-xs font-semibold text-yellow-500">Low Impact</span>
                  </div>
                  <span class="w-2.5 h-2.5 rounded bg-yellow-500"></span>
                </label>

                <!-- Non-Economic / Holiday -->
                <label 
                  class="flex items-center justify-between p-2.5 bg-slate-950 border rounded-lg cursor-pointer hover:border-slate-700 transition"
                  :class="selectedImpacts.includes('Holiday') ? 'border-slate-500/50 bg-slate-800/10' : 'border-slate-800'"
                >
                  <div class="flex items-center space-x-2">
                    <input type="checkbox" value="Holiday" v-model="selectedImpacts" class="rounded border-slate-800 text-slate-400 focus:ring-slate-400 bg-slate-900 w-3.5 h-3.5">
                    <span class="text-xs font-semibold text-slate-400">Non-Economic / Holiday</span>
                  </div>
                  <span class="w-2.5 h-2.5 rounded bg-slate-500"></span>
                </label>
              </div>
            </div>

          </div>

          <!-- RIGHT COLUMN: DATE PAGINATION & ECONOMIC CALENDAR TABLE -->
          <div class="lg:col-span-3 space-y-4">
            
            <!-- Date Pagination Bar (Forex Factory-style) -->
            <div class="bg-slate-900 border border-slate-800/80 rounded-xl p-4 flex flex-col sm:flex-row items-center justify-between shadow-lg shadow-black/25 gap-4">
              <div class="flex items-center space-x-3">
                <!-- Prev Button -->
                <button @click="prevPeriod" class="p-2 bg-slate-950 border border-slate-800 hover:border-slate-700 rounded-lg text-slate-400 hover:text-white transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path></svg>
                </button>
                <!-- Active Label -->
                <span class="text-sm font-extrabold text-slate-200 tracking-wide uppercase font-mono px-1 min-w-[150px] text-center select-none">
                  {{ activeRangeLabel }}
                </span>
                <!-- Next Button -->
                <button @click="nextPeriod" class="p-2 bg-slate-950 border border-slate-800 hover:border-slate-700 rounded-lg text-slate-400 hover:text-white transition">
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path></svg>
                </button>
              </div>
              
              <!-- View Mode Tabs -->
              <div class="flex items-center space-x-1.5 bg-slate-950 p-1 rounded-lg border border-slate-850 text-xs">
                <button 
                  @click="viewMode = 'day'" 
                  class="px-4 py-1.5 rounded-md font-bold transition duration-200"
                  :class="viewMode === 'day' ? 'bg-emerald-500 text-slate-950 shadow-md font-extrabold' : 'text-slate-450 hover:text-slate-200'"
                >
                  Day
                </button>
                <button 
                  @click="viewMode = 'week'" 
                  class="px-4 py-1.5 rounded-md font-bold transition duration-200"
                  :class="viewMode === 'week' ? 'bg-emerald-500 text-slate-950 shadow-md font-extrabold' : 'text-slate-450 hover:text-slate-200'"
                >
                  Week
                </button>
                <button 
                  @click="viewMode = 'month'" 
                  class="px-4 py-1.5 rounded-md font-bold transition duration-200"
                  :class="viewMode === 'month' ? 'bg-emerald-500 text-slate-950 shadow-md font-extrabold' : 'text-slate-450 hover:text-slate-200'"
                >
                  Month
                </button>
              </div>
            </div>

            <!-- Economic Calendar Events Table Card -->
            <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden shadow-xl shadow-black/30">
              
              <!-- Empty state -->
              <div v-if="groupedEvents.length === 0" class="py-16 text-center">
                <svg class="w-12 h-12 mx-auto text-slate-650 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-slate-400 font-medium">No events found matching the active range and filters.</p>
                <p class="text-xs text-slate-500 mt-1">Try selecting a different date range or checking additional filters in the sidebar.</p>
              </div>

              <!-- Calendar content -->
              <div v-else class="overflow-x-auto">
                <table class="w-full border-collapse">
                  
                  <!-- Table Header -->
                  <thead class="bg-slate-950 border-b border-slate-800">
                    <tr>
                      <th class="w-20 px-4 py-3 text-left text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Time</th>
                      <th class="w-16 px-4 py-3 text-left text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Currency</th>
                      <th class="w-16 px-4 py-3 text-center text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Impact</th>
                      <th class="w-12 px-2 py-3 text-center text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Detail</th>
                      <th class="px-4 py-3 text-left text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Event</th>
                      <th class="w-24 px-4 py-3 text-right text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Actual</th>
                      <th class="w-24 px-4 py-3 text-right text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Forecast</th>
                      <th class="w-24 px-4 py-3 text-right text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Previous</th>
                    </tr>
                  </thead>
                  
                  <!-- Loop Days -->
                  <tbody v-for="day in groupedEvents" :key="day.dateLabel">
                    
                    <!-- Day Header Sub-row -->
                    <tr class="bg-slate-950/70 border-y border-slate-800/80">
                      <td colspan="8" class="px-4 py-2 text-xs font-bold text-emerald-400 uppercase tracking-wider font-mono">
                        {{ day.dateLabel }}
                      </td>
                    </tr>
                    
                    <!-- Loop Events in Day -->
                    <template v-for="event in day.events" :key="getEventId(event)">
                      
                      <!-- Main Event Row -->
                      <tr 
                        class="hover:bg-slate-850/30 border-b border-slate-800 transition cursor-pointer"
                        :class="expandedEvents.has(getEventId(event)) ? 'bg-slate-850/20' : ''"
                        @click="toggleEvent(getEventId(event))"
                      >
                        <!-- Time -->
                        <td class="px-4 py-3.5 text-xs text-slate-300 font-medium font-mono whitespace-nowrap">
                          {{ formatEventTime(event.date, selectedTimezone, timeFormat24) }}
                        </td>
                        
                        <!-- Currency Flag / Badge -->
                        <td class="px-4 py-3.5 text-xs text-slate-200 font-semibold whitespace-nowrap">
                          <span class="mr-1" title="Currency flag">{{ currencyMeta[event.country]?.flag || '🏳️' }}</span>
                          {{ event.country }}
                        </td>
                        
                        <!-- Volatility Folder Icons (High / Medium / Low / Holiday) -->
                        <td class="px-4 py-3.5 text-center">
                          <div class="inline-flex justify-center">
                            <!-- High (Red folder) -->
                            <span 
                              v-if="event.impact === 'High'" 
                              class="px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-red-950/40 text-red-400 border border-red-800/40"
                              title="High Impact Event"
                            >
                              High
                            </span>
                            <!-- Medium (Orange folder) -->
                            <span 
                              v-else-if="event.impact === 'Medium'" 
                              class="px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-orange-950/40 text-orange-400 border border-orange-800/40"
                              title="Medium Impact Event"
                            >
                              Med
                            </span>
                            <!-- Low (Yellow folder) -->
                            <span 
                              v-else-if="event.impact === 'Low'" 
                              class="px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-yellow-950/40 text-yellow-400 border border-yellow-800/40"
                              title="Low Impact Event"
                            >
                              Low
                            </span>
                            <!-- Holiday (Gray folder) -->
                            <span 
                              v-else 
                              class="px-1.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-slate-800 text-slate-400 border border-slate-700/60"
                              title="Non-Economic / Holiday"
                            >
                              Holi
                            </span>
                          </div>
                        </td>
                        
                        <!-- Detail Drawer Toggle Icon -->
                        <td class="px-2 py-3.5 text-center">
                          <button 
                            class="text-slate-405 hover:text-emerald-400 focus:outline-none transition p-1"
                            title="View Event Details"
                          >
                            <svg 
                              class="w-4.5 h-4.5 transform transition-transform" 
                              :class="expandedEvents.has(getEventId(event)) ? 'rotate-90 text-emerald-400' : ''"
                              fill="none" 
                              stroke="currentColor" 
                              viewBox="0 0 24 24" 
                              xmlns="http://www.w3.org/2000/svg"
                            >
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"></path>
                            </svg>
                          </button>
                        </td>
                        
                        <!-- Event Title -->
                        <td class="px-4 py-3.5 text-xs text-slate-100 font-semibold max-w-xs md:max-w-md truncate">
                          {{ event.title }}
                        </td>
                        
                        <!-- Actual Value (Green if better, Red if worse) -->
                        <td class="px-4 py-3.5 text-xs text-right font-mono whitespace-nowrap">
                          <span :class="getEventActual(event).color">
                            {{ getEventActual(event).value }}
                          </span>
                        </td>
                        
                        <!-- Forecast -->
                        <td class="px-4 py-3.5 text-xs text-slate-300 text-right font-mono whitespace-nowrap font-medium">
                          {{ event.forecast || '-' }}
                        </td>
                        
                        <!-- Previous -->
                        <td class="px-4 py-3.5 text-xs text-slate-350 text-right font-mono whitespace-nowrap">
                          {{ event.previous || '-' }}
                        </td>
                      </tr>

                      <!-- Expandable Details Row -->
                      <tr v-if="expandedEvents.has(getEventId(event))" class="bg-slate-950/40 border-b border-slate-800">
                        <td colspan="8" class="p-6">
                          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 text-xs text-slate-300">
                            
                            <div class="space-y-3">
                              <div>
                                <span class="block text-[10px] text-slate-500 uppercase font-semibold">Source Release Agency</span>
                                <span class="text-slate-200 font-medium">{{ getEventDetails(event).source }}</span>
                              </div>
                              <div>
                                <span class="block text-[10px] text-slate-500 uppercase font-semibold">Release Frequency</span>
                                <span class="text-slate-200 font-medium">{{ getEventDetails(event).frequency }}</span>
                              </div>
                            </div>
                            
                            <div class="md:col-span-2 space-y-3">
                              <div>
                                <span class="block text-[10px] text-emerald-400 uppercase font-bold tracking-wider mb-1">Why Traders Care</span>
                                <p class="text-slate-300 leading-relaxed font-normal bg-slate-900/50 p-3 rounded-lg border border-slate-800">
                                  {{ getEventDetails(event).whyTradersCare }}
                                </p>
                              </div>
                              <div class="flex justify-end pt-1">
                                <a 
                                  :href="getEventDetails(event).url"
                                  target="_blank" 
                                  class="inline-flex items-center space-x-1 text-xs text-emerald-400 hover:text-emerald-300 font-semibold"
                                >
                                  <span>View Detailed Historical Graphs on Forex Factory</span>
                                  <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                  </svg>
                                </a>
                              </div>
                            </div>

                          </div>
                        </td>
                      </tr>

                    </template>
                  </tbody>
                </table>
              </div>

            </div>

          </div>

        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>
