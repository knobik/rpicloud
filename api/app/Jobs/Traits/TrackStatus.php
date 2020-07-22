<?php


namespace App\Jobs\Traits;


use App\Models\Operation;

trait TrackStatus
{
    /**
     * @param Operation|null $operation
     * @return Operation|null
     */
    protected function startTracking(?Operation $operation = null): ?Operation
    {
        $operation = $operation ?? $this->getOperation();
        if (!$operation) {
            return null;
        }

        $operation->started_at = now();
        $operation->save();

        $this->track('Operation started.', $operation);

        return $operation;
    }

    /**
     * @param string $line
     * @param Operation|null $operation
     * @return Operation|null
     */
    protected function log(string $line, ?Operation $operation = null): ?Operation
    {
        $operation = $operation ?? $this->getOperation();
        if (!$operation) {
            return null;
        }

        // otherwise ugly "new line" is first in the logs.
        if (!empty($operation->log)) {
            $operation->log .= "\n$line";
        } else {
            $operation->log = $line;
        }

        $operation->save();

        return $operation;
    }

    /**
     * @param string $description
     * @param Operation|null $operation
     * @return Operation|null
     */
    protected function track(string $description, ?Operation $operation = null): ?Operation
    {
        $operation = $operation ?? $this->getOperation();
        if (!$operation) {
            return null;
        }

        $time = now()->format('H:i:s');
        $this->log("$time >> $description", $operation);

        $operation->description = $description;
        $operation->save();

        return $operation;
    }

    /**
     * @param Operation|null $operation
     * @throws \Exception
     */
    protected function endTracking(?Operation $operation = null): void
    {
        $operation = $operation ?? $this->getOperation();
        if (!$operation) {
            return;
        }

        $operation->finished_at = now();
        $operation->save();
    }

    /**
     * @param string $description
     * @param Operation|null $operation
     * @throws \Exception
     */
    protected function trackError(string $description, ?Operation $operation = null): void
    {
        $operation = $operation ?? $this->getOperation();
        if (!$operation) {
            return;
        }

        $operation->failed = true;
        $operation->description = $description;
        $operation->save();

        $this->endTracking($operation);
    }

    /**
     * @return Operation|null
     */
    protected function getOperation(): ?Operation
    {
        return Operation::where('node_id', '=', $this->getNode()->id)
            ->whereNull('finished_at')
            ->first();
    }
}
