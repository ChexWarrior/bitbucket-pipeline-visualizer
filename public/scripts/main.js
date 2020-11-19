const pipelineSection = document.querySelector('.pipelines');
pipelineSection.addEventListener('htmx:afterSettle', (evt) => {
  const pipelines = pipelineSection.querySelectorAll('.pipeline-info');
  pipelines.forEach((elem, key) => {
    elem.addEventListener('click', (evt) => {
      elem.querySelector('.pipeline-steps').classList.toggle('is-hidden');
    });
  });
});
