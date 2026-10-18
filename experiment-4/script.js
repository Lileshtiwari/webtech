/**
 * TerraScope Global Country Explorer - RESTful Client Engine
 * Asynchronous fetch with comprehensive multi-continent country dataset
 */

let allCountries = [];
let filteredCountries = [];
let activeRegion = 'all';
let currentSearch = '';
let currentSort = 'pop-desc';

// Comprehensive international country dataset (featuring Asia: Pakistan, Nepal, Bangladesh, Sri Lanka, etc.)
const comprehensiveCountries = [
  // --- ASIA ---
  {
    name: { common: "India", official: "Republic of India" },
    capital: ["New Delhi"], region: "Asia", subregion: "Southern Asia", population: 1428627663,
    flags: { svg: "https://flagcdn.com/in.svg", png: "https://flagcdn.com/w320/in.png" },
    currencies: { INR: { name: "Indian Rupee", symbol: "₹" } }, languages: { hin: "Hindi", eng: "English" },
    unMember: true, cca3: "IND", borders: ["BGD", "BTN", "MMR", "CHN", "NPL", "PAK"], timezones: ["UTC+05:30"]
  },
  {
    name: { common: "Pakistan", official: "Islamic Republic of Pakistan" },
    capital: ["Islamabad"], region: "Asia", subregion: "Southern Asia", population: 240485658,
    flags: { svg: "https://flagcdn.com/pk.svg", png: "https://flagcdn.com/w320/pk.png" },
    currencies: { PKR: { name: "Pakistani Rupee", symbol: "₨" } }, languages: { urd: "Urdu", eng: "English" },
    unMember: true, cca3: "PAK", borders: ["AFG", "CHN", "IND", "IRN"], timezones: ["UTC+05:00"]
  },
  {
    name: { common: "Nepal", official: "Federal Democratic Republic of Nepal" },
    capital: ["Kathmandu"], region: "Asia", subregion: "Southern Asia", population: 30034989,
    flags: { svg: "https://flagcdn.com/np.svg", png: "https://flagcdn.com/w320/np.png" },
    currencies: { NPR: { name: "Nepalese Rupee", symbol: "₨" } }, languages: { nep: "Nepali" },
    unMember: true, cca3: "NPL", borders: ["CHN", "IND"], timezones: ["UTC+05:45"]
  },
  {
    name: { common: "Bangladesh", official: "People's Republic of Bangladesh" },
    capital: ["Dhaka"], region: "Asia", subregion: "Southern Asia", population: 169828911,
    flags: { svg: "https://flagcdn.com/bd.svg", png: "https://flagcdn.com/w320/bd.png" },
    currencies: { BDT: { name: "Bangladeshi Taka", symbol: "৳" } }, languages: { ben: "Bengali" },
    unMember: true, cca3: "BGD", borders: ["MMR", "IND"], timezones: ["UTC+06:00"]
  },
  {
    name: { common: "Sri Lanka", official: "Democratic Socialist Republic of Sri Lanka" },
    capital: ["Sri Jayawardenepura Kotte"], region: "Asia", subregion: "Southern Asia", population: 22181000,
    flags: { svg: "https://flagcdn.com/lk.svg", png: "https://flagcdn.com/w320/lk.png" },
    currencies: { LKR: { name: "Sri Lankan Rupee", symbol: "Rs" } }, languages: { sin: "Sinhala", tam: "Tamil" },
    unMember: true, cca3: "LKA", borders: [], timezones: ["UTC+05:30"]
  },
  {
    name: { common: "China", official: "People's Republic of China" },
    capital: ["Beijing"], region: "Asia", subregion: "Eastern Asia", population: 1411778724,
    flags: { svg: "https://flagcdn.com/cn.svg", png: "https://flagcdn.com/w320/cn.png" },
    currencies: { CNY: { name: "Chinese Yuan", symbol: "¥" } }, languages: { zho: "Chinese" },
    unMember: true, cca3: "CHN", borders: ["AFG", "BTN", "IND", "KAZ", "PRK", "KGZ", "LAO", "MNG", "MMR", "NPL", "PAK", "RUS", "TJK", "VNM"], timezones: ["UTC+08:00"]
  },
  {
    name: { common: "Japan", official: "Japan" },
    capital: ["Tokyo"], region: "Asia", subregion: "Eastern Asia", population: 125584838,
    flags: { svg: "https://flagcdn.com/jp.svg", png: "https://flagcdn.com/w320/jp.png" },
    currencies: { JPY: { name: "Japanese Yen", symbol: "¥" } }, languages: { jpn: "Japanese" },
    unMember: true, cca3: "JPN", borders: [], timezones: ["UTC+09:00"]
  },
  {
    name: { common: "South Korea", official: "Republic of Korea" },
    capital: ["Seoul"], region: "Asia", subregion: "Eastern Asia", population: 51780579,
    flags: { svg: "https://flagcdn.com/kr.svg", png: "https://flagcdn.com/w320/kr.png" },
    currencies: { KRW: { name: "South Korean Won", symbol: "₩" } }, languages: { kor: "Korean" },
    unMember: true, cca3: "KOR", borders: ["PRK"], timezones: ["UTC+09:00"]
  },
  {
    name: { common: "Indonesia", official: "Republic of Indonesia" },
    capital: ["Jakarta"], region: "Asia", subregion: "South-Eastern Asia", population: 273523615,
    flags: { svg: "https://flagcdn.com/id.svg", png: "https://flagcdn.com/w320/id.png" },
    currencies: { IDR: { name: "Indonesian Rupiah", symbol: "Rp" } }, languages: { ind: "Indonesian" },
    unMember: true, cca3: "IDN", borders: ["TLS", "MYS", "PNG"], timezones: ["UTC+07:00"]
  },
  {
    name: { common: "Singapore", official: "Republic of Singapore" },
    capital: ["Singapore"], region: "Asia", subregion: "South-Eastern Asia", population: 5685807,
    flags: { svg: "https://flagcdn.com/sg.svg", png: "https://flagcdn.com/w320/sg.png" },
    currencies: { SGD: { name: "Singapore Dollar", symbol: "$" } }, languages: { eng: "English", zho: "Chinese", msa: "Malay", tam: "Tamil" },
    unMember: true, cca3: "SGP", borders: [], timezones: ["UTC+08:00"]
  },
  {
    name: { common: "Malaysia", official: "Malaysia" },
    capital: ["Kuala Lumpur"], region: "Asia", subregion: "South-Eastern Asia", population: 32365999,
    flags: { svg: "https://flagcdn.com/my.svg", png: "https://flagcdn.com/w320/my.png" },
    currencies: { MYR: { name: "Malaysian Ringgit", symbol: "RM" } }, languages: { msa: "Malay" },
    unMember: true, cca3: "MYS", borders: ["BRN", "IDN", "THA"], timezones: ["UTC+08:00"]
  },
  {
    name: { common: "Bhutan", official: "Kingdom of Bhutan" },
    capital: ["Thimphu"], region: "Asia", subregion: "Southern Asia", population: 771608,
    flags: { svg: "https://flagcdn.com/bt.svg", png: "https://flagcdn.com/w320/bt.png" },
    currencies: { BTN: { name: "Bhutanese Ngultrum", symbol: "Nu." } }, languages: { dzo: "Dzongkha" },
    unMember: true, cca3: "BTN", borders: ["CHN", "IND"], timezones: ["UTC+06:00"]
  },
  {
    name: { common: "United Arab Emirates", official: "United Arab Emirates" },
    capital: ["Abu Dhabi"], region: "Asia", subregion: "Western Asia", population: 9890400,
    flags: { svg: "https://flagcdn.com/ae.svg", png: "https://flagcdn.com/w320/ae.png" },
    currencies: { AED: { name: "United Arab Emirates Dirham", symbol: "د.إ" } }, languages: { ara: "Arabic" },
    unMember: true, cca3: "ARE", borders: ["OMN", "SAU"], timezones: ["UTC+04:00"]
  },
  {
    name: { common: "Saudi Arabia", official: "Kingdom of Saudi Arabia" },
    capital: ["Riyadh"], region: "Asia", subregion: "Western Asia", population: 34813871,
    flags: { svg: "https://flagcdn.com/sa.svg", png: "https://flagcdn.com/w320/sa.png" },
    currencies: { SAR: { name: "Saudi Riyal", symbol: "﷼" } }, languages: { ara: "Arabic" },
    unMember: true, cca3: "SAU", borders: ["IRQ", "JOR", "KWT", "OMN", "QAT", "ARE", "YEM"], timezones: ["UTC+03:00"]
  },
  // --- EUROPE ---
  {
    name: { common: "United Kingdom", official: "United Kingdom of Great Britain and Northern Ireland" },
    capital: ["London"], region: "Europe", subregion: "Northern Europe", population: 67215293,
    flags: { svg: "https://flagcdn.com/gb.svg", png: "https://flagcdn.com/w320/gb.png" },
    currencies: { GBP: { name: "British Pound", symbol: "£" } }, languages: { eng: "English" },
    unMember: true, cca3: "GBR", borders: ["IRL"], timezones: ["UTC+00:00"]
  },
  {
    name: { common: "Germany", official: "Federal Republic of Germany" },
    capital: ["Berlin"], region: "Europe", subregion: "Western Europe", population: 83240525,
    flags: { svg: "https://flagcdn.com/de.svg", png: "https://flagcdn.com/w320/de.png" },
    currencies: { EUR: { name: "Euro", symbol: "€" } }, languages: { deu: "German" },
    unMember: true, cca3: "DEU", borders: ["AUT", "BEL", "CZE", "DNK", "FRA", "LUX", "NLD", "POL", "CHE"], timezones: ["UTC+01:00"]
  },
  {
    name: { common: "France", official: "French Republic" },
    capital: ["Paris"], region: "Europe", subregion: "Western Europe", population: 67391582,
    flags: { svg: "https://flagcdn.com/fr.svg", png: "https://flagcdn.com/w320/fr.png" },
    currencies: { EUR: { name: "Euro", symbol: "€" } }, languages: { fra: "French" },
    unMember: true, cca3: "FRA", borders: ["AND", "BEL", "DEU", "ITA", "LUX", "MCO", "ESP", "CHE"], timezones: ["UTC+01:00"]
  },
  {
    name: { common: "Italy", official: "Italian Republic" },
    capital: ["Rome"], region: "Europe", subregion: "Southern Europe", population: 59554023,
    flags: { svg: "https://flagcdn.com/it.svg", png: "https://flagcdn.com/w320/it.png" },
    currencies: { EUR: { name: "Euro", symbol: "€" } }, languages: { ita: "Italian" },
    unMember: true, cca3: "ITA", borders: ["AUT", "FRA", "SMR", "SVN", "CHE", "VAT"], timezones: ["UTC+01:00"]
  },
  {
    name: { common: "Spain", official: "Kingdom of Spain" },
    capital: ["Madrid"], region: "Europe", subregion: "Southern Europe", population: 47351567,
    flags: { svg: "https://flagcdn.com/es.svg", png: "https://flagcdn.com/w320/es.png" },
    currencies: { EUR: { name: "Euro", symbol: "€" } }, languages: { spa: "Spanish" },
    unMember: true, cca3: "ESP", borders: ["AND", "FRA", "GIB", "PRT", "MAR"], timezones: ["UTC+01:00"]
  },
  {
    name: { common: "Switzerland", official: "Swiss Confederation" },
    capital: ["Bern"], region: "Europe", subregion: "Western Europe", population: 8654622,
    flags: { svg: "https://flagcdn.com/ch.svg", png: "https://flagcdn.com/w320/ch.png" },
    currencies: { CHF: { name: "Swiss Franc", symbol: "CHF" } }, languages: { deu: "German", fra: "French", ita: "Italian" },
    unMember: true, cca3: "CHE", borders: ["AUT", "FRA", "ITA", "LIE", "DEU"], timezones: ["UTC+01:00"]
  },
  // --- AMERICAS ---
  {
    name: { common: "United States", official: "United States of America" },
    capital: ["Washington, D.C."], region: "Americas", subregion: "North America", population: 331893745,
    flags: { svg: "https://flagcdn.com/us.svg", png: "https://flagcdn.com/w320/us.png" },
    currencies: { USD: { name: "United States Dollar", symbol: "$" } }, languages: { eng: "English" },
    unMember: true, cca3: "USA", borders: ["CAN", "MEX"], timezones: ["UTC-05:00"]
  },
  {
    name: { common: "Canada", official: "Canada" },
    capital: ["Ottawa"], region: "Americas", subregion: "North America", population: 38005238,
    flags: { svg: "https://flagcdn.com/ca.svg", png: "https://flagcdn.com/w320/ca.png" },
    currencies: { CAD: { name: "Canadian Dollar", symbol: "$" } }, languages: { eng: "English", fra: "French" },
    unMember: true, cca3: "CAN", borders: ["USA"], timezones: ["UTC-05:00"]
  },
  {
    name: { common: "Brazil", official: "Federative Republic of Brazil" },
    capital: ["Brasília"], region: "Americas", subregion: "South America", population: 212559409,
    flags: { svg: "https://flagcdn.com/br.svg", png: "https://flagcdn.com/w320/br.png" },
    currencies: { BRL: { name: "Brazilian Real", symbol: "R$" } }, languages: { por: "Portuguese" },
    unMember: true, cca3: "BRA", borders: ["ARG", "BOL", "COL", "PRY", "PER", "URY", "VEN"], timezones: ["UTC-03:00"]
  },
  {
    name: { common: "Mexico", official: "United Mexican States" },
    capital: ["Mexico City"], region: "Americas", subregion: "North America", population: 128932753,
    flags: { svg: "https://flagcdn.com/mx.svg", png: "https://flagcdn.com/w320/mx.png" },
    currencies: { MXN: { name: "Mexican Peso", symbol: "$" } }, languages: { spa: "Spanish" },
    unMember: true, cca3: "MEX", borders: ["BLZ", "GTM", "USA"], timezones: ["UTC-06:00"]
  },
  // --- AFRICA ---
  {
    name: { common: "South Africa", official: "Republic of South Africa" },
    capital: ["Pretoria"], region: "Africa", subregion: "Southern Africa", population: 59308690,
    flags: { svg: "https://flagcdn.com/za.svg", png: "https://flagcdn.com/w320/za.png" },
    currencies: { ZAR: { name: "South African Rand", symbol: "R" } }, languages: { eng: "English", afr: "Afrikaans" },
    unMember: true, cca3: "ZAF", borders: ["BWA", "LSO", "MOZ", "NAM", "ZWE"], timezones: ["UTC+02:00"]
  },
  {
    name: { common: "Egypt", official: "Arab Republic of Egypt" },
    capital: ["Cairo"], region: "Africa", subregion: "Northern Africa", population: 102334403,
    flags: { svg: "https://flagcdn.com/eg.svg", png: "https://flagcdn.com/w320/eg.png" },
    currencies: { EGP: { name: "Egyptian Pound", symbol: "£" } }, languages: { ara: "Arabic" },
    unMember: true, cca3: "EGY", borders: ["ISR", "LBY", "SDN"], timezones: ["UTC+02:00"]
  },
  {
    name: { common: "Nigeria", official: "Federal Republic of Nigeria" },
    capital: ["Abuja"], region: "Africa", subregion: "Western Africa", population: 206139587,
    flags: { svg: "https://flagcdn.com/ng.svg", png: "https://flagcdn.com/w320/ng.png" },
    currencies: { NGN: { name: "Nigerian Naira", symbol: "₦" } }, languages: { eng: "English" },
    unMember: true, cca3: "NGA", borders: ["BEN", "CMR", "TCD", "NER"], timezones: ["UTC+01:00"]
  },
  {
    name: { common: "Kenya", official: "Republic of Kenya" },
    capital: ["Nairobi"], region: "Africa", subregion: "Eastern Africa", population: 53771300,
    flags: { svg: "https://flagcdn.com/ke.svg", png: "https://flagcdn.com/w320/ke.png" },
    currencies: { KES: { name: "Kenyan Shilling", symbol: "Sh" } }, languages: { eng: "English", swa: "Swahili" },
    unMember: true, cca3: "KEN", borders: ["ETH", "SOM", "SSD", "TZA", "UGA"], timezones: ["UTC+03:00"]
  },
  // --- OCEANIA ---
  {
    name: { common: "Australia", official: "Commonwealth of Australia" },
    capital: ["Canberra"], region: "Oceania", subregion: "Australia and New Zealand", population: 25687041,
    flags: { svg: "https://flagcdn.com/au.svg", png: "https://flagcdn.com/w320/au.png" },
    currencies: { AUD: { name: "Australian Dollar", symbol: "$" } }, languages: { eng: "English" },
    unMember: true, cca3: "AUS", borders: [], timezones: ["UTC+10:00"]
  },
  {
    name: { common: "New Zealand", official: "New Zealand" },
    capital: ["Wellington"], region: "Oceania", subregion: "Australia and New Zealand", population: 5084300,
    flags: { svg: "https://flagcdn.com/nz.svg", png: "https://flagcdn.com/w320/nz.png" },
    currencies: { NZD: { name: "New Zealand Dollar", symbol: "$" } }, languages: { eng: "English" },
    unMember: true, cca3: "NZL", borders: [], timezones: ["UTC+12:00"]
  }
];

async function fetchCountryData() {
  const loading = document.getElementById('loadingState');
  const errorBox = document.getElementById('errorState');
  const grid = document.getElementById('countriesGrid');

  loading.style.display = 'block';
  errorBox.style.display = 'none';
  grid.style.display = 'none';

  try {
    const url = 'https://restcountries.com/v3.1/all?fields=name,capital,region,subregion,population,flags,currencies,languages,unMember,cca3,borders,timezones';
    const response = await fetch(url, { signal: AbortSignal.timeout(10000) });

    if (!response.ok) throw new Error(`HTTP Error: ${response.status}`);
    const data = await response.json();
    allCountries = data;
  } catch (err) {
    console.warn('REST API unavailable or timed out; activating comprehensive multi-continent dataset.', err);
    allCountries = comprehensiveCountries;
    errorBox.style.display = 'block';
  } finally {
    loading.style.display = 'none';
    grid.style.display = 'grid';
    updateTelemetry();
    applyFilters();
  }
}

function updateTelemetry() {
  const totalNations = allCountries.length;
  let totalPop = 0;
  let unCount = 0;

  allCountries.forEach(c => {
    totalPop += c.population || 0;
    if (c.unMember) unCount++;
  });

  document.getElementById('statTotalNations').innerText = totalNations;
  document.getElementById('statGlobalPop').innerText = (totalPop / 1e9).toFixed(2) + 'B';
  document.getElementById('statUnMembers').innerText = unCount;
}

function applyFilters() {
  let list = [...allCountries];

  // Region filter
  if (activeRegion !== 'all') {
    list = list.filter(c => c.region === activeRegion);
  }

  // Search filter
  if (currentSearch.trim()) {
    const q = currentSearch.toLowerCase().trim();
    list = list.filter(c => {
      const common = c.name?.common?.toLowerCase() || '';
      const official = c.name?.official?.toLowerCase() || '';
      const capital = (c.capital && c.capital[0]) ? c.capital[0].toLowerCase() : '';
      const cca3 = c.cca3?.toLowerCase() || '';
      return common.includes(q) || official.includes(q) || capital.includes(q) || cca3.includes(q);
    });
  }

  // Sort
  if (currentSort === 'pop-desc') {
    list.sort((a, b) => (b.population || 0) - (a.population || 0));
  } else if (currentSort === 'pop-asc') {
    list.sort((a, b) => (a.population || 0) - (b.population || 0));
  } else if (currentSort === 'name-asc') {
    list.sort((a, b) => (a.name?.common || '').localeCompare(b.name?.common || ''));
  } else if (currentSort === 'name-desc') {
    list.sort((a, b) => (b.name?.common || '').localeCompare(a.name?.common || ''));
  }

  filteredCountries = list;
  document.getElementById('statVisibleCount').innerText = list.length;
  renderCountries(list);
}

function renderCountries(list) {
  const grid = document.getElementById('countriesGrid');
  const empty = document.getElementById('emptyState');

  if (list.length === 0) {
    grid.innerHTML = '';
    empty.style.display = 'block';
    return;
  }

  empty.style.display = 'none';

  grid.innerHTML = list.map((country, index) => {
    const name = country.name?.common || 'Unknown Nation';
    const capital = (country.capital && country.capital[0]) ? country.capital[0] : 'None';
    const pop = (country.population || 0).toLocaleString();
    const flagUrl = country.flags?.svg || country.flags?.png || '';
    const region = country.region || 'Global';
    const unBadge = country.unMember ? '<span class="flag-un-badge">UN Member</span>' : '';

    // Extract currency code & symbol
    let currText = 'N/A';
    if (country.currencies) {
      const keys = Object.keys(country.currencies);
      if (keys.length > 0) {
        const c = country.currencies[keys[0]];
        currText = `${keys[0]} (${c.symbol || ''})`;
      }
    }

    return `
      <article class="country-card" onclick="openDossier(${index})">
        <!-- Square-shaped boxed container with full flag visible -->
        <div class="card-flag-wrap">
          <img src="${flagUrl}" alt="${name} Flag" class="card-flag-img" loading="lazy">
          <span class="flag-region-badge">${region}</span>
          ${unBadge}
        </div>
        <div class="card-content">
          <h3 class="card-country-name">${name}</h3>
          <span class="card-capital">Capital: ${capital}</span>
          <ul class="card-specs-list">
            <li>
              <span class="spec-label">Population:</span>
              <span class="spec-val">${pop}</span>
            </li>
            <li>
              <span class="spec-label">Currency:</span>
              <span class="spec-val">${currText}</span>
            </li>
            <li>
              <span class="spec-label">ISO Code:</span>
              <span class="spec-val">${country.cca3 || '---'}</span>
            </li>
          </ul>
          <span class="btn-card-dossier">View Country Details &rarr;</span>
        </div>
      </article>
    `;
  }).join('');
}

function setRegionFilter(region) {
  activeRegion = region;
  document.querySelectorAll('.region-pills .pill-btn').forEach(btn => {
    btn.classList.remove('active');
  });
  event.target.classList.add('active');
  applyFilters();
}

function handleSearch(val) {
  currentSearch = val;
  const clearBtn = document.getElementById('btnClearSearch');
  clearBtn.style.display = val ? 'block' : 'none';
  applyFilters();
}

function clearSearch() {
  document.getElementById('searchInput').value = '';
  document.getElementById('btnClearSearch').style.display = 'none';
  currentSearch = '';
  applyFilters();
}

function handleSort(val) {
  currentSort = val;
  applyFilters();
}

function resetFilters() {
  activeRegion = 'all';
  currentSearch = '';
  document.getElementById('searchInput').value = '';
  document.getElementById('btnClearSearch').style.display = 'none';
  document.querySelectorAll('.region-pills .pill-btn').forEach((btn, i) => {
    if (i === 0) btn.classList.add('active');
    else btn.classList.remove('active');
  });
  applyFilters();
}

/* Dossier Modal */
function openDossier(index) {
  const country = filteredCountries[index];
  if (!country) return;

  const commonName = country.name?.common || '';
  document.getElementById('modalCountryName').innerText = commonName;
  document.getElementById('modalOfficialName').innerText = country.name?.official || '';
  document.getElementById('modalFlag').src = country.flags?.svg || country.flags?.png || '';
  document.getElementById('modalCapital').innerText = (country.capital && country.capital[0]) || 'N/A';
  document.getElementById('modalRegion').innerText = `${country.region} · ${country.subregion || ''}`;
  document.getElementById('modalPopulation').innerText = (country.population || 0).toLocaleString();

  // Currencies
  let currStr = 'N/A';
  if (country.currencies) {
    currStr = Object.values(country.currencies).map(c => `${c.name} (${c.symbol || ''})`).join(', ');
  }
  document.getElementById('modalCurrencies').innerText = currStr;

  // Languages
  let langStr = 'N/A';
  if (country.languages) {
    langStr = Object.values(country.languages).join(', ');
  }
  document.getElementById('modalLanguages').innerText = langStr;

  document.getElementById('modalCca3').innerText = country.cca3 || 'N/A';
  document.getElementById('modalUnStatus').innerText = country.unMember ? 'Full Sovereign Member State' : 'Observer / Non-Member';
  document.getElementById('modalTimezones').innerText = (country.timezones && country.timezones.join(', ')) || 'N/A';

  // Borders
  const borderWrap = document.getElementById('modalBorders');
  if (country.borders && country.borders.length > 0) {
    borderWrap.innerHTML = country.borders.map(b => `<span class="border-pill-tag">${b}</span>`).join('');
  } else {
    borderWrap.innerHTML = '<span style="color: #94A3B8; font-size: 0.8rem;">Island nation (No land borders)</span>';
  }

  // Reliable Google Maps link
  const mapLink = document.getElementById('modalMapLink');
  mapLink.href = `https://www.google.com/maps/search/?api=1&query=${encodeURIComponent(commonName)}`;

  document.getElementById('dossierModal').classList.add('is-active');
}

function closeDossierModal() {
  document.getElementById('dossierModal').classList.remove('is-active');
}

document.addEventListener('keydown', (e) => {
  if (e.key === 'Escape') closeDossierModal();
});

document.addEventListener('click', (e) => {
  const modal = document.getElementById('dossierModal');
  if (e.target === modal) closeDossierModal();
});

// Initial boot
document.addEventListener('DOMContentLoaded', () => {
  fetchCountryData();
});
