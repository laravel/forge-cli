<?php

namespace App\Commands\Concerns;

trait InteractsWithEvents
{
    /**
     * The buffer of events output.
     *
     * @var array
     */
    protected $outputBuffer = [];

    /**
     * Displays the event output while the given condition is "true".
     *
     * @param  string  $username
     * @param  string|int  $eventId
     * @param  callable|null  $while
     * @return void
     */
    protected function displayEventOutput($username, $eventId, $while = null)
    {
        if ($while) {
            $this->outputBuffer[$eventId] = [];
        }

        $firstOutput = false;

        do {
            if ($while) {
                $this->time->sleep(1);
            }

            [$exitCode, $output] = $this->remote->exec(sprintf(
                'cat /home/%s/.forge/provision-%s.output',
                $username,
                $eventId
            ));

            if ($while && ! $firstOutput && ! empty($output)) {
                $this->line('');

                $firstOutput = true;
            }

            if ($exitCode == 0) {
                $this->displayOutput(collect($output)->slice(count($this->outputBuffer[$eventId]))
                    ->map('trim'));

                $this->outputBuffer[$eventId] = $output;
            }
        } while ($while && call_user_func($while));

        $while ? $this->displayEventOutput($username, $eventId) : $this->line('');
    }

    /**
     * Display the output of the given collection indenting each line.
     *
     * @param  \Illuminate\Support\Collection  $collection
     * @return void
     */
    protected function displayOutput($collection)
    {
        $collection->map('trim')
            ->filter(function ($line) {
                return ! empty($line);
            })->each(function ($line) {
                return $this->line("  <fg=#6C7280>▕</> $line");
            });
    }

    /**
     * Find the first event by the given "description".
     *
     * @param  string  $description
     * @return string|int
     */
    protected function findEventId($description)
    {
        $eventId = optional(collect($this->forge->events((string) $this->currentServer()->id))->first(function ($event) use ($description) {
            return $event->description == $description;
        }))->id;

        abort_if(is_null($eventId), 1, 'Event unresolvable.');

        return $eventId;
    }
}
