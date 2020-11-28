<?php

namespace Chexwarrior;

class FakeBitbucket
{
    private $faker;

    public function __construct()
    {
        $this->faker = \Faker\Factory::create();
    }

    public function getRepositories(): array
    {
        $repos = [];
        $amt = $this->faker->numberBetween(5, 20);
        for ($i = 0; $i < $amt; $i += 1) {
            $repos[] = $this->faker->word;
        }

        return $repos;
    }

    public function getPipelines(): array
    {
        $pipelineStatuses = [
            // Completed
            'SUCCESS',
            'FAILED',
            'EXPIRED',
            'ERROR',
            'STOPPED',
            // In Progress
            'RUNNING',
            'PAUSED',
            // Other but counting as In Progress
            'PENDING',
        ];

        $pipelines = [];
        $amt = $this->faker->numberBetween(5, 10);
        for ($i = 0; $i < $amt; $i += 1) {
            $pipelineStatus = $pipelineStatuses[array_rand($pipelineStatuses)];
            $pipelineInfo = [
                'repository' => $this->faker->word,
                'build_number' => $this->faker->numberBetween(1, 500),
                'status' => $pipelineStatus,
            ];

            $stepAmt = $this->faker->numberBetween(1, 10);
            $stepInfo = [];
            for ($j = 0; $j < $stepAmt; $j += 1) {
                $previousStep = ($j - 1 >= 0) ? $stepInfo[$j - 1] : null;
                $isFinalStep = $j + 1 === $stepAmt;
                $stepInfo[] = [
                    'name' => $this->faker->word,
                    'status' => $this->getPipelineStepStatus($pipelineStatus, $previousStep, $isFinalStep),
                ];
            }

            $pipelineInfo['steps'] = $stepInfo;
            $pipelines[] = $pipelineInfo;
        }

        return $pipelines;
    }

    private function getPipelineStepStatus(string $pipelineStatus, ?array $previousStep, bool $isFinalStep): string
    {
        $stepStatus = '';
        $statuses = [
            // Completed
            'ERROR',
            'FAILED',
            'SUCCESS',
            'EXPIRED',
            'STOPPED',
            'NOT RUN',
            // In Progress
            'PENDING',
            'READY',
            'RUNNING',
        ];

        /**
         * If $pipelineStatus is...
         * SUCCESS we know that all steps were SUCCESS
         * FAILED we know that one of the steps has a status of FAILED and that all steps after have a status of NOT RUN
         * EXPIRED we know that one of the steps has a status of EXPIRED and that all steps after have a status of NOT RUN
         * ERROR we know that one of the steps has a status of ERROR and that all steps after have a status of NOT RUN
         * STOPPED we know that one of the steps has a status of STOPPED and that all steps after have a status of NOT RUN
         * RUNNING we know that the previous steps must be SUCCESS and one of the steps must have a status of RUNNING and the steps afterwards have a status of NOT RUN
         * PAUSED we know that the previous steps must be SUCCESS and one of the steps must have a status of RUNNING and the steps afterwards have a status of NOT RUN
         * PENDING we know that previous steps must be SUCCESS and one of the steps must have a status of PENDING and the steps aftewards have a status of NOT RUN
         */

        if ($pipelineStatus === 'SUCCESS') {
            $stepStatus = 'SUCCESS';
        } else if ($pipelineStatus === 'FAILED' || $pipelineStatus === 'EXPIRED' ||
            $pipelineStatus === 'ERROR' || $pipelineStatus === 'STOPPED') {
            $validStepStatuses = ['SUCCESS', $pipelineStatus];
            // If this step is the first step or the previous step was SUCCESS this step could either be SUCCESS or FAILED/EXPIRED/ERROR/STOPPED
            if ($previousStep === null || $previousStep['status'] === 'SUCCESS') {
                $stepStatus = $validStepStatuses[array_rand($validStepStatuses)];
            }
            // If the previous step has a status equal to FAILED/EXPIRED/ERROR/STOPPED or NOT RUN than this step has a status of NOT RUN
            else {
                $stepStatus = 'NOT RUN';
            }

            // If this is the last step in the pipeline and none of the other steps are FAILED/EXPIRED/ERROR/STOPPPED than change this one to that status
            if ($isFinalStep && $stepStatus === 'SUCCESS') {
                $stepStatus = $pipelineStatus;
            }
        } else if ($pipelineStatus === 'RUNNING' || $pipelineStatus === 'PENDING') {
            $validStepStatuses = ['SUCCESS', $pipelineStatus];
            // If this is the first step or the previous step has a status of SUCCESS this step could either be SUCCESS or RUNNING
            if ($previousStep === null || $previousStep['status'] === 'SUCCESS') {
                $stepStatus = $validStepStatuses[array_rand($validStepStatuses)];
            }
            // If the previous step has a status of RUNNING than this step has a status of PENDING
            else if ($previousStep['status'] === $pipelineStatus) {
                $stepStatus = 'PENDING';
            }

            if ($isFinalStep && $previousStep['status'] === 'SUCCESS') {
                $stepStatus = $pipelineStatus;
            }
        } else if ($pipelineStatus === 'PAUSED') {
            $stepStatus = 'PENDING';
        }

        return $stepStatus;
    }
}
