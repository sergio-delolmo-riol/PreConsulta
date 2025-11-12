// DOM Elements
const welcomeSection = document.getElementById('welcome');
const triageFormSection = document.getElementById('triageForm');
const resultsSection = document.getElementById('results');
const startTriageBtn = document.getElementById('startTriageBtn');
const backBtn = document.getElementById('backBtn');
const newEvaluationBtn = document.getElementById('newEvaluationBtn');
const triageFormElement = document.getElementById('triageFormElement');
const painLevelSlider = document.getElementById('painLevel');
const painLevelValue = document.getElementById('painLevelValue');

// Event Listeners
startTriageBtn.addEventListener('click', showTriageForm);
backBtn.addEventListener('click', showWelcome);
newEvaluationBtn.addEventListener('click', resetAndShowWelcome);
triageFormElement.addEventListener('submit', handleSubmit);
painLevelSlider.addEventListener('input', updatePainLevel);

// Update pain level display
function updatePainLevel() {
    painLevelValue.textContent = painLevelSlider.value;
}

// Navigation functions
function showWelcome() {
    welcomeSection.classList.remove('hidden');
    triageFormSection.classList.add('hidden');
    resultsSection.classList.add('hidden');
}

function showTriageForm() {
    welcomeSection.classList.add('hidden');
    triageFormSection.classList.remove('hidden');
    resultsSection.classList.add('hidden');
}

function showResults() {
    welcomeSection.classList.add('hidden');
    triageFormSection.classList.add('hidden');
    resultsSection.classList.remove('hidden');
}

function resetAndShowWelcome() {
    triageFormElement.reset();
    painLevelValue.textContent = '0';
    showWelcome();
}

// Form submission handler
function handleSubmit(e) {
    e.preventDefault();
    
    // Collect form data
    const formData = {
        age: parseInt(document.getElementById('age').value),
        symptoms: Array.from(document.querySelectorAll('input[name="symptoms"]:checked'))
            .map(cb => cb.value),
        duration: document.getElementById('duration').value,
        painLevel: parseInt(painLevelSlider.value),
        conditions: Array.from(document.querySelectorAll('input[name="conditions"]:checked'))
            .map(cb => cb.value),
        additionalInfo: document.getElementById('additionalInfo').value
    };
    
    // Calculate triage level
    const triageResult = calculateTriage(formData);
    
    // Display results
    displayResults(triageResult);
    
    // Show results section
    showResults();
}

// Triage calculation logic
function calculateTriage(data) {
    let urgencyScore = 0;
    let urgencyLevel = 'low';
    let color = 'low';
    
    // Critical symptoms that require immediate attention
    const criticalSymptoms = [
        'chest-pain',
        'difficulty-breathing',
        'loss-consciousness',
        'severe-bleeding'
    ];
    
    // High priority symptoms
    const highPrioritySymptoms = [
        'severe-headache',
        'high-fever',
        'abdominal-pain',
        'confusion'
    ];
    
    // Check for critical symptoms
    const hasCriticalSymptoms = data.symptoms.some(s => criticalSymptoms.includes(s));
    if (hasCriticalSymptoms) {
        urgencyScore += 40;
    }
    
    // Check for high priority symptoms
    const hasHighPrioritySymptoms = data.symptoms.some(s => highPrioritySymptoms.includes(s));
    if (hasHighPrioritySymptoms) {
        urgencyScore += 25;
    }
    
    // Pain level assessment
    if (data.painLevel >= 8) {
        urgencyScore += 30;
    } else if (data.painLevel >= 5) {
        urgencyScore += 15;
    } else if (data.painLevel >= 3) {
        urgencyScore += 5;
    }
    
    // Age considerations
    if (data.age < 2 || data.age > 65) {
        urgencyScore += 10;
    }
    
    // Preexisting conditions
    if (data.conditions.length > 0) {
        urgencyScore += 10;
    }
    
    // Duration assessment
    if (data.duration === 'hours' && urgencyScore > 20) {
        urgencyScore += 5;
    }
    
    // Determine urgency level based on score
    if (urgencyScore >= 50 || hasCriticalSymptoms) {
        urgencyLevel = 'emergency';
        color = 'emergency';
    } else if (urgencyScore >= 30) {
        urgencyLevel = 'high';
        color = 'high';
    } else if (urgencyScore >= 15) {
        urgencyLevel = 'medium';
        color = 'medium';
    } else {
        urgencyLevel = 'low';
        color = 'low';
    }
    
    return {
        level: urgencyLevel,
        color: color,
        score: urgencyScore,
        data: data
    };
}

// Display results
function displayResults(result) {
    const resultContent = document.getElementById('resultContent');
    
    let title, icon, message, recommendations;
    
    switch(result.level) {
        case 'emergency':
            title = 'URGENCIA INMEDIATA';
            icon = '🚨';
            message = 'Los síntomas indicados requieren atención médica INMEDIATA. Por favor, diríjase a urgencias o llame al 112 sin demora.';
            recommendations = [
                'Acuda inmediatamente al servicio de urgencias',
                'Si no puede desplazarse, llame al 112',
                'No conduzca si se siente mal, pida ayuda',
                'Tenga a mano su historial médico si es posible'
            ];
            break;
            
        case 'high':
            title = 'PRIORIDAD ALTA';
            icon = '⚠️';
            message = 'Sus síntomas requieren atención médica pronto. Se recomienda acudir a urgencias o contactar con su médico en las próximas horas.';
            recommendations = [
                'Acuda a urgencias en las próximas 2-4 horas',
                'Si los síntomas empeoran, no espere',
                'Mantenga a alguien informado de su estado',
                'Prepare su tarjeta sanitaria y documentación'
            ];
            break;
            
        case 'medium':
            title = 'PRIORIDAD MODERADA';
            icon = '⚡';
            message = 'Sus síntomas deben ser evaluados por un profesional médico. Considere solicitar cita con su médico de atención primaria en las próximas 24-48 horas.';
            recommendations = [
                'Solicite cita con su médico de cabecera',
                'Vigile la evolución de los síntomas',
                'Si empeoran, considere acudir a urgencias',
                'Descanse y manténgase hidratado',
                'Anote cualquier cambio en los síntomas'
            ];
            break;
            
        case 'low':
            title = 'PRIORIDAD BAJA';
            icon = '✅';
            message = 'Sus síntomas parecen leves. Puede solicitar cita con su médico de atención primaria si persisten o empeoran.';
            recommendations = [
                'Observe la evolución de los síntomas',
                'Descanse adecuadamente',
                'Mantenga una buena hidratación',
                'Si los síntomas persisten más de una semana, consulte con su médico',
                'Anote cualquier cambio o nuevo síntoma'
            ];
            break;
    }
    
    // Clear existing content
    resultContent.innerHTML = '';
    
    // Create result card
    const resultCard = document.createElement('div');
    resultCard.className = `result-card ${result.color}`;
    
    const iconDiv = document.createElement('div');
    iconDiv.className = 'icon';
    iconDiv.textContent = icon;
    
    const titleH3 = document.createElement('h3');
    titleH3.textContent = title;
    
    const messageP = document.createElement('p');
    messageP.style.fontSize = '1.2rem';
    messageP.style.marginTop = '1rem';
    messageP.textContent = message;
    
    resultCard.appendChild(iconDiv);
    resultCard.appendChild(titleH3);
    resultCard.appendChild(messageP);
    
    // Create recommendations section
    const recommendationDiv = document.createElement('div');
    recommendationDiv.className = 'recommendation';
    
    const recH4 = document.createElement('h4');
    recH4.textContent = 'Recomendaciones:';
    
    const recUl = document.createElement('ul');
    recommendations.forEach(r => {
        const li = document.createElement('li');
        li.textContent = r;
        recUl.appendChild(li);
    });
    
    recommendationDiv.appendChild(recH4);
    recommendationDiv.appendChild(recUl);
    
    // Create summary section
    const summaryDiv = document.createElement('div');
    summaryDiv.className = 'recommendation';
    
    const summaryH4 = document.createElement('h4');
    summaryH4.textContent = 'Resumen de su evaluación:';
    
    const ageP = document.createElement('p');
    const ageStrong = document.createElement('strong');
    ageStrong.textContent = 'Edad:';
    ageP.appendChild(ageStrong);
    ageP.appendChild(document.createTextNode(` ${result.data.age} años`));
    
    const painP = document.createElement('p');
    const painStrong = document.createElement('strong');
    painStrong.textContent = 'Nivel de dolor:';
    painP.appendChild(painStrong);
    painP.appendChild(document.createTextNode(` ${result.data.painLevel}/10`));
    
    const durationP = document.createElement('p');
    const durationStrong = document.createElement('strong');
    durationStrong.textContent = 'Duración:';
    durationP.appendChild(durationStrong);
    durationP.appendChild(document.createTextNode(` ${getDurationText(result.data.duration)}`));
    
    const symptomsP = document.createElement('p');
    const symptomsStrong = document.createElement('strong');
    symptomsStrong.textContent = 'Síntomas reportados:';
    symptomsP.appendChild(symptomsStrong);
    symptomsP.appendChild(document.createTextNode(` ${result.data.symptoms.length}`));
    
    summaryDiv.appendChild(summaryH4);
    summaryDiv.appendChild(ageP);
    summaryDiv.appendChild(painP);
    summaryDiv.appendChild(durationP);
    summaryDiv.appendChild(symptomsP);
    
    if (result.data.conditions.length > 0) {
        const conditionsP = document.createElement('p');
        const conditionsStrong = document.createElement('strong');
        conditionsStrong.textContent = 'Condiciones preexistentes:';
        conditionsP.appendChild(conditionsStrong);
        conditionsP.appendChild(document.createTextNode(` ${result.data.conditions.length}`));
        summaryDiv.appendChild(conditionsP);
    }
    
    // Create warning section
    const warningDiv = document.createElement('div');
    warningDiv.className = 'warning';
    warningDiv.textContent = '⚠️ ';
    const warningStrong = document.createElement('strong');
    warningStrong.textContent = 'Importante:';
    warningDiv.appendChild(warningStrong);
    warningDiv.appendChild(document.createTextNode(' Esta evaluación es orientativa y no sustituye el diagnóstico médico profesional. Si tiene dudas o sus síntomas empeoran, busque atención médica inmediata.'));
    
    // Append all sections
    resultContent.appendChild(resultCard);
    resultContent.appendChild(recommendationDiv);
    resultContent.appendChild(summaryDiv);
    resultContent.appendChild(warningDiv);
}

function getDurationText(duration) {
    const durationMap = {
        'hours': 'Menos de 24 horas',
        'days': '1-3 días',
        'week': 'Más de una semana'
    };
    return durationMap[duration] || duration;
}

// Initialize pain level display
updatePainLevel();
