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
            'SUCCESS',
            'FAILED',
            'EXPIRED',
            'ERROR',
            'CANCELED',
            'IN PROGRESS',
            'PENDING',
            'PAUSED',
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
                $previousStep = ($j - 1 >= 0) ? $stepInfo[$j] : null;
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
            'ERROR',
            'FAILED',
            'SUCCESSFUL',
            'EXPIRED',
            'STOPPED',
            'NOT RUN',
            'PENDING',
            'READY',
            'IN PROGRESS',
        ];

        /**
         * If $pipelineStatus is...
         * SUCCESSFUL we know that all steps were SUCCESSFUL
         * FAILED we know that one of the steps has a status of FAILED and that all steps after have a status of NOT RUN
         * EXPIRED we know that one of the steps has a status of EXPIRED and that all steps after have a status of NOT RUN
         * ERROR we know that one of the steps has a status of ERROR and that all steps after have a status of NOT RUN
         * STOPPED we know that one of the steps has a status of STOPPED and that all steps after have a status of NOT RUN
         * IN PROGRESS we know that the previous steps must be SUCCESSFUL and one of the steps must have a status of IN PROGRESS and the steps afterwards have a status of NOT RUN
         * PAUSED we know that the previous steps must be SUCCESSFUL and one of the steps must have a status of IN PROGRESS and the steps afterwards have a status of NOT RUN
         * PENDING we know that previous steps must be SUCCESSFUL and one of the steps must have a status of PENDING and the steps aftewards have a status of NOT RUN
         */

        if ($pipelineStatus === 'SUCCESSFUL') {
            $stepStatus = 'SUCCESSFUL';
        } else if ($pipelineStatus === 'FAILED' || $pipelineStatus === 'EXPIRED' ||
            $pipelineStatus === 'ERROR' || $pipelineStatus === 'STOPPED') {
            $validStepStatuses = ['SUCCESSFUL', $pipelineStatus];
            // If this step is the first step or the previous step was SUCCESSFUL this step could either be SUCCESSFUL or FAILED/EXPIRED/ERROR/STOPPED
            if ($previousStep === null || $previousStep['status'] === 'SUCCESSFUL') {
                $stepStatus = $validStepStatuses[array_rand($validStepStatuses)];
            }
            // If the previous step has a status equal to FAILED/EXPIRED/ERROR/STOPPED or NOT RUN than this step has a status of NOT RUN
            else {
                $stepStatus = 'NOT RUN';
            }

            // If this is the last step in the pipeline and none of the other steps are FAILED/EXPIRED/ERROR/STOPPPED than change this one to that status
            if ($isFinalStep && $stepStatus === 'SUCCESSFUL') {
                $stepStatus = $pipelineStatus;
            }
        } else if ($pipelineStatus === 'IN PROGRESS' || $pipelineStatus === 'PENDING' ||
            $pipelineStatus === 'PAUSED') {
            $validStepStatuses = ['SUCCESSFUL', $pipelineStatus];
            // If this is the first step or the previous step has a status of SUCCESSFUL this step could either be SUCCESSFUL or IN PROGRESS
            if ($previousStep === null || $previousStep['status'] === 'SUCCESSFUL') {
                $stepStatus = $validStepStatuses[array_rand($validStepStatuses)];
            }
            // If the previous step has a status of IN PROGRESS than this step has a status of PENDING
            else if ($previousStep === $pipelineStatus) {
                $stepStatus = 'PENDING';
            }

            if ($isFinalStep && $stepStatus === 'SUCCESSFUL') {
                $stepStatus = $pipelineStatus;
            }
        }

        return $stepStatus;
    }
}
