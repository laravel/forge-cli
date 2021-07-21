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
     * @param  string|int  $eventId
     * @param  callable|null  $while
     * @return void
     */
    protected function displayEventOutput($eventId, $while = null)
    {
        $while && $this->outputBuffer[$eventId] = [];

        $firstOutput = false;

        do {
            [$exitCode, $output] = $this->remote->exec(sprintf(
                'cat /home/forge/.forge/provision-%s.output',
                $eventId
            ));

            if ($while && ! $firstOutput && ! empty($output)) {
                $this->line('');

                $firstOutput = true;
            }

            if ($exitCode == 0) {
                collect($output)->slice(count($this->outputBuffer[$eventId]))
                    ->map('trim')
                    ->filter(function ($line) {
                        return ! empty($line);
                    })->each(function ($line) {
                        $this->line("  <fg=#6C7280>▕</> $line");
                    });

                $this->outputBuffer[$eventId] = $output;
            }
        } while ($while && call_user_func($while));

        $while ? $this->displayEventOutput($eventId) : $this->line('');
    }
}
