<?php

namespace App\Commands\Concerns;

use Illuminate\Contracts\Support\Arrayable;
use Symfony\Component\Console\Question\ChoiceQuestion;

use function Laravel\Prompts\search;

trait InteractsWithIO
{
    /**
     * Format input to textual table.
     *
     * @param  array  $headers
     * @param  Arrayable|array  $rows
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
        $name = $this->argument('site');

        $answers = collect($this->forge->sites($this->currentServer()->id));

        abort_if($answers->isEmpty(), 1, 'This server does not have any sites.');

        if (! is_null($name)) {
            return optional($answers->where('name', $name)->first())->id ?: $name;
        }

        return search($question, function (string $value) use ($answers) {
            return $answers
                ->filter(function ($site) use ($value) {
                    return str_contains(strtolower($site->name), strtolower($value));
                })
                ->mapWithKeys(function ($site) {
                    return [$site->id => $site->name];
                })
                ->all();
        });
    }

    /**
     * Prompt the user for an "server" input.
     *
     * @param  string  $question
     * @return string|int
     */
    public function askForServer($question)
    {
        $name = $this->argument('server');

        $answers = collect($this->forge->servers($this->currentOrganization())->lazy())
            ->reject(function ($server) {
                return $server->revoked;
            });

        abort_if($answers->isEmpty(), 1, 'This account does not have any servers.');

        if (! is_null($name)) {
            return optional($answers->firstWhere('name', $name))->id ?: $name;
        }

        return search($question, function (string $value) use ($answers) {
            return $answers
                ->filter(function ($server) use ($value) {
                    return str_contains(strtolower($server->name), strtolower($value));
                })
                ->mapWithKeys(function ($server) {
                    return [$server->id => $server->name];
                })
                ->all();
        });
    }

    /**
     * Prompt the user for an "organization" input.
     *
     * @param  string  $question
     * @return string
     */
    public function askForOrganization($question)
    {
        if (! is_null($slug = $this->argument('organization'))) {
            return $slug;
        }

        $answers = collect($this->forge->organizations());

        abort_if($answers->isEmpty(), 1, 'This account does not belong to any organizations.');

        return search($question, function (string $value) use ($answers) {
            return $answers
                ->filter(function ($organization) use ($value) {
                    return str_contains(strtolower($organization->name), strtolower($value))
                        || str_contains(strtolower($organization->slug), strtolower($value));
                })
                ->mapWithKeys(function ($organization) {
                    return [$organization->slug => $organization->name];
                })
                ->all();
        });
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

        return search($question, function (string $value) use ($answers) {
            return $answers
                ->filter(function ($daemon) use ($value) {
                    return str_contains(strtolower($daemon->command), strtolower($value));
                })
                ->mapWithKeys(function ($daemon) {
                    return [$daemon->id => $daemon->command];
                })
                ->all();
        });
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
