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
        $pipelines = [];
        $amt = $this->faker->numberBetween(5, 10);
        for ($i = 0; $i < $amt; $i += 1) {
            $pipelineInfo = [
                'repository' => $this->faker->word,
                'number' => $this->faker->numberBetween(1, 500),
            ];

            $stepAmt = $this->faker->numberBetween(1, 10);
            $stepInfo = [];
            for ($j = 0; $j < $stepAmt; $j += 1) {
                $stepInfo[] = [
                    'name' => $this->faker->word,
                ];
            }

            $pipelineInfo['steps'] = $stepInfo;
            $pipelines[] = $pipelineInfo;
        }

        return $pipelines;
    }

}
