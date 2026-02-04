<?php

namespace App\Commands\Concerns;

use Laravel\Forge\Resources\Server;
use Symfony\Component\Console\Question\ChoiceQuestion;

trait InteractsWithIO
{
    /**
     * Format input to textual table.
     *
     * @param  array  $headers
     * @param  \Illuminate\Contracts\Support\Arrayable|array  $rows
     * @param  string  $tableStyle
     * @return void
     */
    public function table($headers, $rows, $tableStyle = 'default', array $columnStyles = [])
    {
        $this->line('');

        parent::table(
            collect($headers)->map(function ($header) {
                return "   <comment>$header</comment>";
            })->all(),
            collect($rows)->map(function ($row) {
                return collect($row)->map(function ($cell) {
                    return "   <options=bold>$cell</>";
                })->all();
            })->all(),
            'compact'
        );

        $this->line('');
    }

    /**
     * Prompt the user for an "site" input.
     *
     * @param  string  $question
     * @return string|int
     */
    public function askForSite($question)
    {
        $name = $this->hasArgument('site') ? $this->argument('site') : null;

        $answers = collect($this->forge->sites($this->currentServer()->id));

        abort_if($answers->isEmpty(), 1, 'This server does not have any sites.');

        // Priority 1: Command argument
        if (! is_null($name)) {
            return optional($answers->where('name', $name)->first())->id ?: $name;
        }

        // Priority 2: Environment config (named environments or legacy .forge)
        $envSiteId = $this->getEnvironmentSiteId();

        if ($envSiteId) {
            return $envSiteId;
        }

        // Priority 3: Interactive prompt
        return $this->choiceStep($question, $answers->mapWithKeys(function ($resource) {
            return [$resource->id => $resource->name];
        })->all());
    }

    /**
     * Prompt the user for an "server" input.
     *
     * @param  string  $question
     * @return string|int
     */
    public function askForServer($question)
    {
        $name = $this->hasArgument('server') ? $this->argument('server') : null;

        $answers = collect($this->forge->servers());

        abort_if($answers->isEmpty(), 1, 'This account does not have any servers.');

        if (! is_null($name)) {
            return optional($answers->where('name', $name)->first())->id ?: $name;
        }

        return $this->choiceStep($question, $answers->mapWithKeys(function ($resource) {
            /** @var \Laravel\Forge\Resources\Server $resource */
            $tags = ! empty($resource->tags) ? " ({$resource->tags()})" : null;

            return [$resource->id => $resource->name.$tags];
        })->all());
    }

    /**
     * Prompt the user for an "daemon" input.
     *
     * @param  string  $question
     * @return string|int
     */
    public function askForDaemon($question)
    {
        $command = $this->argument('daemon');

        $answers = collect($this->forge->daemons($this->currentServer()->id));

        abort_if($answers->isEmpty(), 1, 'This server does not have any daemons.');

        if (! is_null($command)) {
            return optional($answers->where('command', $command)->first())->id ?: $command;
        }

        return $this->choiceStep($question, $answers->mapWithKeys(function ($resource) {
            return [$resource->id => $resource->command];
        })->all());
    }

    /**
     * Display a "step" message.
     *
     * @param  string|array  $text
     * @return void
     */
    public function step($text)
    {
        $text = $this->formatStepText($text);

        $this->line('<fg=blue>==></> <options=bold>'.$text.'</>');
    }

    /**
     * Display a successful "step" message.
     *
     * @param  string|array  $text
     * @return void
     */
    public function successfulStep($text)
    {
        $text = $this->formatStepText($text);

        $this->line('<fg=green>==></> <options=bold>'.$text.'</>');
    }

    /**
     * Display a warn "step" message.
     *
     * @param  string|array  $text
     * @return void
     */
    public function warnStep($text)
    {
        $text = $this->formatStepText($text);

        $this->line('<fg=yellow>==></> <options=bold>'.$text.'</>');
    }

    /**
     * Display a ask "step" message.
     *
     * @param  string|array  $question
     * @param  string|null  $default
     * @return mixed
     */
    public function askStep($question, $default = null)
    {
        $question = $this->formatStepText($question);

        return $this->ask('<fg=yellow>‣</> <options=bold>'.$question.'</>', $default);
    }

    /**
     * Display a confirm "step" message.
     *
     * @param  string|array  $question
     * @param  bool  $default
     * @return bool
     */
    public function confirmStep($question, $default = false)
    {
        $question = $this->formatStepText($question);

        return $this->output->confirm('<fg=yellow>‣</> <options=bold>'.$question.'</>', $default);
    }

    /**
     * Display a secret "step" message.
     *
     * @param  array|string  $question
     * @return mixed
     */
    public function secretStep($question)
    {
        $question = $this->formatStepText($question);

        return $this->secret('<fg=yellow>‣</> <options=bold>'.$question.'</>');
    }

    /**
     * Formats a text step.
     *
     * @param  string|array  $text
     * @return string
     */
    protected function formatStepText($text)
    {
        $parameters = [];

        if (is_array($text)) {
            $parameters = $text;
            $text = array_shift($text);
            unset($parameters[0]);
        }

        return sprintf(ucwords($text), ...collect($parameters)->map(function ($parameter) {
            return '<comment>['.$parameter.']</comment>';
        })->values()->all());
    }

    /**
     * Display a ask "step" message.
     *
     * @param  string|array  $question
     * @param  array  $choices
     * @param  string|null  $default
     * @return int
     */
    public function choiceStep($question, $choices, $default = null)
    {
        $question = $this->formatStepText($question);

        $question = new class('<fg=yellow>‣</> <options=bold>'.$question.'</>', $choices, $default) extends ChoiceQuestion
        {
            /**
             * Determines if the given array is associative.
             */
            public function isAssoc(array $array): bool
            {
                return true;
            }
        };

        return (int) $this->output->askQuestion($question);
    }
}
