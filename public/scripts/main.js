const pipelineSection = document.querySelector('.pipelines');
pipelineSection.addEventListener('htmx:afterSettle', (evt) => {
  const pipelines = pipelineSection.querySelectorAll('.pipeline-info');
  pipelines.forEach((elem, key) => {
    elem.addEventListener('click', (evt) => {
      const titleIcon = elem.querySelector('.title svg');
      const steps = elem.querySelector('.pipeline-steps');
      steps.classList.toggle('active');
      steps.classList.toggle('inactive');
      titleIcon.classList.toggle('is-hidden');
    });
  });
});
