function parseMetricValue(id) {
  const el = document.getElementById(id);
  if (!el) return 0;
  const numeric = Number(el.textContent.replace(/[^0-9.\-]/g, ""));
  return isNaN(numeric) ? 0 : numeric;
}

function formatMetric(id, value) {
  if (id === "revenue") {
    return "₱" + value.toLocaleString("en-US", { minimumFractionDigits: 2, maximumFractionDigits: 2 });
  }
  return Math.round(value).toLocaleString();
}

function animateValue(id, start, end, duration) {
  const obj = document.getElementById(id);
  if (!obj) return;

  const range = end - start;
  if (range === 0) {
    obj.innerHTML = formatMetric(id, end);
    return;
  }

  const stepTime = 16; // ~60fps
  const totalSteps = Math.max(Math.round(duration / stepTime), 1);
  let currentStep = 0;

  const timer = setInterval(() => {
    currentStep++;
    const progress = Math.min(currentStep / totalSteps, 1);
    const current = start + range * progress;
    obj.innerHTML = formatMetric(id, current);

    if (currentStep >= totalSteps) {
      obj.innerHTML = formatMetric(id, end);
      clearInterval(timer);
    }
  }, stepTime);
}

animateValue("revenue", 0, parseMetricValue("revenue"), 1500);
animateValue("tickets", 0, parseMetricValue("tickets"), 1000);
animateValue("movies", 0, parseMetricValue("movies"), 800);
animateValue("screenings", 0, parseMetricValue("screenings"), 800);

