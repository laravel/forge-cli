#compdef forge
# Forge CLI Zsh Completion (oh-my-zsh compatible)

_forge_environments() {
    local forge_file=""
    local dir="$PWD"

    while [[ "$dir" != "/" ]]; do
        if [[ -f "$dir/.forge" ]]; then
            forge_file="$dir/.forge"
            break
        fi
        dir="$(dirname "$dir")"
    done

    if [[ -n "$forge_file" && -f "$forge_file" ]]; then
        cat "$forge_file" 2>/dev/null | grep -o '"[a-zA-Z0-9_-]*"[[:space:]]*:[[:space:]]*{' | grep -v '"environments"' | sed 's/"//g' | sed 's/[[:space:]]*:[[:space:]]*{//'
    fi
}

_forge() {
    local -a commands
    commands=(
        'deploy:Deploy a site'
        'init:Initialize a .forge config file'
        'config:Display the local .forge configuration'
        'config\:set:Add or update an environment'
        'config\:remove:Remove an environment'
        'config\:default:Set the default environment'
        'completion:Generate shell completion script'
        'login:Authenticate with Laravel Forge'
        'logout:Logout from Laravel Forge'
        'ssh:Start an SSH session'
        'tinker:Tinker with a site'
        'open:Open a site in forge.laravel.com'
        'server\:list:List the servers'
        'server\:switch:Switch to a different server'
        'server\:current:Determine your current server'
        'site\:list:List the sites'
        'site\:logs:Retrieve the latest site log messages'
        'env\:pull:Download the environment file'
        'env\:push:Upload the environment file'
        'deploy\:logs:Retrieve deployment log messages'
        'daemon\:list:List the daemons'
        'daemon\:logs:Retrieve daemon log messages'
        'daemon\:restart:Restart a daemon'
        'daemon\:status:Get daemon status'
        'database\:logs:Retrieve database log messages'
        'database\:restart:Restart the database'
        'database\:shell:Start a database shell'
        'database\:status:Get database status'
        'nginx\:logs:Retrieve Nginx log messages'
        'nginx\:restart:Restart Nginx'
        'nginx\:status:Get Nginx status'
        'php\:logs:Retrieve PHP log messages'
        'php\:restart:Restart PHP'
        'php\:status:Get PHP status'
        'ssh\:configure:Configure SSH key authentication'
        'ssh\:test:Test SSH key authentication'
    )

    # Complete first argument (command)
    if (( CURRENT == 2 )); then
        _describe -t commands 'forge command' commands
        return
    fi

    # Complete based on command
    case "${words[2]}" in
        deploy)
            if (( CURRENT == 3 )); then
                local -a envs
                envs=(${(f)"$(_forge_environments)"})
                if [[ -n "$envs" ]]; then
                    _describe -t environments 'environment' envs
                fi
            else
                _arguments \
                    '--site=[Explicit site name]:site:' \
                    '--force[Skip confirmation]'
            fi
            ;;
        config:set)
            if (( CURRENT == 3 )); then
                local -a envs
                envs=(${(f)"$(_forge_environments)"} 'production' 'staging' 'dev')
                _describe -t environments 'environment' envs
            else
                _arguments \
                    '--confirm[Require confirmation]' \
                    '--no-confirm[Disable confirmation]'
            fi
            ;;
        config:remove|config:default)
            if (( CURRENT == 3 )); then
                local -a envs
                envs=(${(f)"$(_forge_environments)"})
                if [[ -n "$envs" ]]; then
                    _describe -t environments 'environment' envs
                fi
            fi
            ;;
        init)
            _arguments \
                '--force[Overwrite existing]' \
                '--simple[Simple config]'
            ;;
        completion)
            if (( CURRENT == 3 )); then
                _describe -t shells 'shell' '(zsh bash)'
            else
                _arguments '--install[Install to shell config]'
            fi
            ;;
    esac
}

compdef _forge forge
