<?php


namespace App\Jobs\Traits;


use App\Models\JobStatus;

trait TrackStatus
{
    /**
     * @param string $description
     * @return JobStatus
     */
    protected function track(string $description): JobStatus
    {
        $jobStatus = $this->getJobStatus();

        $jobStatus->description = $description;
        $jobStatus->save();

        return $jobStatus;
    }

    /**
     * @param string $name
     */
    protected function startTracking(string $name)
    {
        $jobStatus = new JobStatus();

        $jobStatus->name = $name;
        $jobStatus->description = '';
        $jobStatus->node_id = $this->getNode()->id;
        $jobStatus->save();
    }

    /**
     * @param JobStatus|null $jobStatus
     * @throws \Exception
     */
    protected function endTracking(?JobStatus $jobStatus = null): void
    {
        $jobStatus = $jobStatus ?? $this->getJobStatus();
        $jobStatus->delete();
    }

    /**
     * @param string $description
     * @throws \Exception
     */
    protected function trackError(string $description)
    {
        $jobStatus = $this->getJobStatus();

        $jobStatus->failed = true;
        $jobStatus->description = $description;
        $jobStatus->save();

        $this->endTracking($jobStatus);
    }

    /**
     * @return JobStatus
     */
    protected function getJobStatus(): JobStatus
    {
        return JobStatus::where('node_id', '=', $this->getNode()->id)
            ->whereNull('finished_at')
            ->first();
    }
}
