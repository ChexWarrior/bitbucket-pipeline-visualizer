const pipelineSection = document.querySelector('.pipelines');
// pipelineSection.addEventListener('htmx:afterSettle', function (evt) {
  const infoCards = pipelineSection.querySelectorAll('.pipeline-info');
  infoCards.forEach((element) => {
    const steps = element.querySelector('.pipeline-steps');
    element.addEventListener('click', function (e) {
      steps.classList.toggle('hidden');
    })
  });
// });
