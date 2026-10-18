/**
 * VitalScale Pro - Clinical Calculation Engine
 * Body Mass Index (BMI), Basal Metabolic Rate (BMR) & WHO Classifications
 */

let currentUnitSystem = 'metric'; // 'metric' or 'imperial'
let historyLog = [
  {
    timestamp: '2026-10-18 09:15',
    profile: 'Male, 24y',
    height: '175 cm',
    weight: '68 kg',
    bmi: '22.2',
    category: 'Normal Weight',
    badgeClass: 'badge-normal'
  },
  {
    timestamp: '2026-10-17 18:30',
    profile: 'Female, 29y',
    height: '162 cm',
    weight: '74 kg',
    bmi: '28.2',
    category: 'Overweight',
    badgeClass: 'badge-over'
  }
];

function setUnitSystem(system) {
  currentUnitSystem = system;
  const btnMetric = document.getElementById('btnMetric');
  const btnImperial = document.getElementById('btnImperial');
  const metricH = document.getElementById('metricHeightGroup');
  const imperialH = document.getElementById('imperialHeightGroup');
  const metricW = document.getElementById('metricWeightGroup');
  const imperialW = document.getElementById('imperialWeightGroup');

  if (system === 'metric') {
    btnMetric.classList.add('active');
    btnImperial.classList.remove('active');
    metricH.style.display = 'block';
    imperialH.style.display = 'none';
    metricW.style.display = 'block';
    imperialW.style.display = 'none';
  } else {
    btnMetric.classList.remove('active');
    btnImperial.classList.add('active');
    metricH.style.display = 'none';
    imperialH.style.display = 'block';
    metricW.style.display = 'none';
    imperialW.style.display = 'block';
  }
  calculateBMI();
}

function syncHeightFromNumber(val) {
  const num = parseFloat(val) || 170;
  document.getElementById('sliderHeightCm').value = num;
  document.getElementById('displayHeightCm').innerText = num + ' cm';
  calculateBMI();
}

function syncHeightFromSlider(val) {
  document.getElementById('heightCm').value = val;
  document.getElementById('displayHeightCm').innerText = val + ' cm';
  calculateBMI();
}

function syncWeightFromNumber(val) {
  const num = parseFloat(val) || 65;
  document.getElementById('sliderWeightKg').value = num;
  document.getElementById('displayWeightKg').innerText = num + ' kg';
  calculateBMI();
}

function syncWeightFromSlider(val) {
  document.getElementById('weightKg').value = val;
  document.getElementById('displayWeightKg').innerText = val + ' kg';
  calculateBMI();
}

function updateImperialHeight() {
  const ft = parseInt(document.getElementById('heightFeet').value) || 5;
  const inches = parseInt(document.getElementById('heightInches').value) || 0;
  document.getElementById('displayHeightImp').innerText = `${ft} ft ${inches} in`;
  calculateBMI();
}

function updateImperialWeight(val) {
  const lbs = parseFloat(val) || 150;
  document.getElementById('sliderWeightLbs').value = lbs;
  document.getElementById('displayWeightLbs').innerText = lbs + ' lbs';
  calculateBMI();
}

function updateImperialWeightFromSlider(val) {
  document.getElementById('weightLbs').value = val;
  document.getElementById('displayWeightLbs').innerText = val + ' lbs';
  calculateBMI();
}

function calculateBMI() {
  let heightMeters = 1.75;
  let weightKg = 68;

  if (currentUnitSystem === 'metric') {
    const cm = parseFloat(document.getElementById('heightCm').value) || 175;
    weightKg = parseFloat(document.getElementById('weightKg').value) || 68;
    heightMeters = cm / 100;
  } else {
    const ft = parseInt(document.getElementById('heightFeet').value) || 5;
    const inch = parseInt(document.getElementById('heightInches').value) || 9;
    const lbs = parseFloat(document.getElementById('weightLbs').value) || 150;
    const totalInches = (ft * 12) + inch;
    heightMeters = totalInches * 0.0254;
    weightKg = lbs * 0.45359237;
  }

  if (heightMeters <= 0 || weightKg <= 0) return;

  // Formula: weight / (height * height)
  const bmi = weightKg / (heightMeters * heightMeters);
  const roundedBMI = bmi.toFixed(1);

  // Update Score Display
  document.getElementById('bmiValue').innerText = roundedBMI;

  // Gauge Angle Calculation
  // Range: 15 to 35 BMI mapped to -90deg to +90deg
  let angle = -90 + ((bmi - 15) / (35 - 15)) * 180;
  if (angle < -90) angle = -90;
  if (angle > 90) angle = 90;
  document.getElementById('gaugeNeedle').style.transform = `rotate(${angle}deg)`;

  // Classification logic
  const badgeEl = document.getElementById('bmiBadge');
  const healthyPill = document.getElementById('bmiHealthyPill');
  const riskVal = document.getElementById('healthRiskVal');
  const adviceText = document.getElementById('adviceText');

  badgeEl.className = 'badge-status';

  if (bmi < 18.5) {
    badgeEl.classList.add('badge-under');
    badgeEl.innerText = 'Underweight';
    healthyPill.innerText = 'Below Threshold';
    riskVal.innerText = 'Elevated Risk';
    riskVal.style.color = '#0284C7';
    adviceText.innerText = 'Your BMI indicates lower than ideal body mass. Consider nutrient-dense whole foods, progressive resistance training, and consulting a healthcare professional to identify any underlying metabolic factors.';
  } else if (bmi <= 24.9) {
    badgeEl.classList.add('badge-normal');
    badgeEl.innerText = 'Normal Weight';
    healthyPill.innerText = 'Optimal Zone';
    riskVal.innerText = 'Lowest Risk';
    riskVal.style.color = '#10B981';
    adviceText.innerText = 'Your body composition falls within the clinically recommended weight range. Maintain balanced macronutrient intake, ensure 150 minutes of weekly aerobic exercise, and sustain current lifestyle habits.';
  } else if (bmi <= 29.9) {
    badgeEl.classList.add('badge-over');
    badgeEl.innerText = 'Overweight';
    healthyPill.innerText = 'Pre-Obese Zone';
    riskVal.innerText = 'Moderate Risk';
    riskVal.style.color = '#D97706';
    adviceText.innerText = 'Your BMI falls slightly above standard clinical benchmarks. Prioritize a mild caloric deficit (-300 kcal/day), increase daily step count, and incorporate whole fiber-rich vegetables into your diet.';
  } else {
    badgeEl.classList.add('badge-obese');
    badgeEl.innerText = 'Obese';
    healthyPill.innerText = 'High Clinical Risk';
    riskVal.innerText = 'High Risk';
    riskVal.style.color = '#DC2626';
    adviceText.innerText = 'Elevated BMI correlates with increased cardiovascular strain. We recommend clinical consultation with an endocrinologist or certified nutritionist for structured lifestyle intervention.';
  }

  // Ideal Weight Range: BMI 18.5 to 24.9
  const minIdealKg = (18.5 * (heightMeters * heightMeters)).toFixed(1);
  const maxIdealKg = (24.9 * (heightMeters * heightMeters)).toFixed(1);

  if (currentUnitSystem === 'metric') {
    document.getElementById('idealWeightVal').innerText = `${minIdealKg} – ${maxIdealKg} kg`;
  } else {
    const minLbs = (minIdealKg * 2.20462).toFixed(1);
    const maxLbs = (maxIdealKg * 2.20462).toFixed(1);
    document.getElementById('idealWeightVal').innerText = `${minLbs} – ${maxLbs} lbs`;
  }

  // BMR & Calorie Estimation (Mifflin-St Jeor)
  const age = parseInt(document.getElementById('ageInput').value) || 24;
  const sex = document.getElementById('genderSelect').value;
  const heightCm = heightMeters * 100;

  let bmr = (10 * weightKg) + (6.25 * heightCm) - (5 * age);
  if (sex === 'male') {
    bmr += 5;
  } else if (sex === 'female') {
    bmr -= 161;
  } else {
    bmr -= 78;
  }

  const roundedBMR = Math.round(bmr);
  const maintenanceCal = Math.round(roundedBMR * 1.4); // Moderate activity multiplier

  document.getElementById('bmrVal').innerText = roundedBMR.toLocaleString() + ' kcal';
  document.getElementById('calorieVal').innerText = maintenanceCal.toLocaleString() + ' kcal';
}

function resetForm() {
  document.getElementById('ageInput').value = '24';
  document.getElementById('genderSelect').value = 'male';
  if (currentUnitSystem === 'metric') {
    document.getElementById('heightCm').value = '175';
    document.getElementById('sliderHeightCm').value = '175';
    document.getElementById('displayHeightCm').innerText = '175 cm';
    document.getElementById('weightKg').value = '68';
    document.getElementById('sliderWeightKg').value = '68';
    document.getElementById('displayWeightKg').innerText = '68 kg';
  } else {
    document.getElementById('heightFeet').value = '5';
    document.getElementById('heightInches').value = '9';
    document.getElementById('weightLbs').value = '150';
    document.getElementById('sliderWeightLbs').value = '150';
    document.getElementById('displayHeightImp').innerText = '5 ft 9 in';
    document.getElementById('displayWeightLbs').innerText = '150 lbs';
  }
  calculateBMI();
}

function saveToHealthLog() {
  const now = new Date();
  const dateStr = now.toISOString().slice(0, 10) + ' ' + now.toTimeString().slice(0, 5);
  const age = document.getElementById('ageInput').value;
  const sex = document.getElementById('genderSelect').value;
  const bmi = document.getElementById('bmiValue').innerText;
  const badgeEl = document.getElementById('bmiBadge');
  const cat = badgeEl.innerText;
  const badgeClass = badgeEl.className.replace('badge-status', '').trim();

  let hStr = '';
  let wStr = '';
  if (currentUnitSystem === 'metric') {
    hStr = document.getElementById('heightCm').value + ' cm';
    wStr = document.getElementById('weightKg').value + ' kg';
  } else {
    const ft = document.getElementById('heightFeet').value;
    const inch = document.getElementById('heightInches').value;
    hStr = `${ft}'${inch}"`;
    wStr = document.getElementById('weightLbs').value + ' lbs';
  }

  historyLog.unshift({
    timestamp: dateStr,
    profile: `${sex.charAt(0).toUpperCase() + sex.slice(1)}, ${age}y`,
    height: hStr,
    weight: wStr,
    bmi: bmi,
    category: cat,
    badgeClass: badgeClass
  });

  renderLogTable();
}

function renderLogTable() {
  const tbody = document.getElementById('logTableBody');
  if (!tbody) return;

  if (historyLog.length === 0) {
    tbody.innerHTML = '<tr><td colspan="7" style="text-align: center; color: #94A3B8; padding: 2rem;">No session measurements recorded yet.</td></tr>';
    return;
  }

  tbody.innerHTML = historyLog.map(item => `
    <tr>
      <td style="font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; color: #64748B;">${item.timestamp}</td>
      <td><strong>${item.profile}</strong></td>
      <td>${item.height}</td>
      <td>${item.weight}</td>
      <td><span style="font-family: 'JetBrains Mono', monospace; font-weight: 800; font-size: 1.05rem;">${item.bmi}</span></td>
      <td><span class="badge-status ${item.badgeClass}">${item.category}</span></td>
      <td><span style="color: #10B981; font-weight: 700; font-size: 0.8rem;">&bull; Recorded</span></td>
    </tr>
  `).join('');
}

function clearLog() {
  historyLog = [];
  renderLogTable();
}

function exportLogCSV() {
  if (historyLog.length === 0) {
    alert('No calculation records to export.');
    return;
  }
  let csv = 'Timestamp,Profile,Height,Weight,BMI,Category\n';
  historyLog.forEach(row => {
    csv += `"${row.timestamp}","${row.profile}","${row.height}","${row.weight}","${row.bmi}","${row.category}"\n`;
  });
  const blob = new Blob([csv], { type: 'text/csv' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.setAttribute('href', url);
  a.setAttribute('download', 'vitalscale_biometric_log.csv');
  a.click();
}

// Initial Calculation on Page Load
document.addEventListener('DOMContentLoaded', () => {
  calculateBMI();
  renderLogTable();
});
