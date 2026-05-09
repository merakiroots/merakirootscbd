$ErrorActionPreference = "Stop"

$root = Split-Path -Parent $PSScriptRoot
$failures = New-Object System.Collections.Generic.List[string]

function Add-Failure {
    param([string] $Message)
    $script:failures.Add($Message) | Out-Null
}

function Assert-True {
    param(
        [bool] $Condition,
        [string] $Message
    )

    if (-not $Condition) {
        Add-Failure $Message
    }
}

function Read-JsonFile {
    param([string] $RelativePath)

    $path = Join-Path $root $RelativePath
    Assert-True (Test-Path $path) "$RelativePath exists"

    if (-not (Test-Path $path)) {
        return $null
    }

    try {
        return Get-Content -Raw $path | ConvertFrom-Json -Depth 32
    } catch {
        Add-Failure "$RelativePath is valid JSON: $($_.Exception.Message)"
        return $null
    }
}

function Resolve-PluginRelativePath {
    param([string] $PluginPath)

    $clean = $PluginPath
    if ($clean.StartsWith("./")) {
        $clean = $clean.Substring(2)
    }

    return Join-Path $root $clean
}

$codexConfigPath = Join-Path $root ".codex/config.toml"
Assert-True (Test-Path $codexConfigPath) ".codex/config.toml exists"
$codexConfig = ""
if (Test-Path $codexConfigPath) {
    $codexConfig = Get-Content -Raw $codexConfigPath
}

$expectedMcpServers = @(
    "github",
    "context7",
    "exa",
    "memory",
    "playwright",
    "sequential-thinking",
    "wordpress-local"
)

foreach ($server in $expectedMcpServers) {
    $pattern = "(?m)^\[mcp_servers\.$([regex]::Escape($server))\]"
    Assert-True ([regex]::IsMatch($codexConfig, $pattern)) ".codex/config.toml defines mcp_servers.$server"
}

Assert-True (-not [regex]::IsMatch($codexConfig, "(?m)^notify\s*=")) ".codex/config.toml leaves external notify unset"
Assert-True ($codexConfig.Contains("--path=D:/merakirootscbd2")) "wordpress-local points at D:/merakirootscbd2"

$agents = @{
    "explorer" = "explorer.toml"
    "reviewer" = "reviewer.toml"
    "docs_researcher" = "docs-researcher.toml"
}

foreach ($entry in $agents.GetEnumerator()) {
    $name = $entry.Key
    $file = $entry.Value
    $agentPath = Join-Path $root ".codex/agents/$file"
    Assert-True (Test-Path $agentPath) ".codex/agents/$file exists"
    Assert-True ($codexConfig.Contains("[agents.$name]")) ".codex/config.toml registers agent $name"
    Assert-True ($codexConfig.Contains("config_file = `"agents/$file`"")) "agent $name points to agents/$file"

    if (Test-Path $agentPath) {
        $agentConfig = Get-Content -Raw $agentPath
        Assert-True ($agentConfig.Contains('sandbox_mode = "read-only"')) "agent $name is read-only"
        Assert-True ($agentConfig.Contains("developer_instructions =")) "agent $name has developer instructions"
    }
}

$plugin = Read-JsonFile ".codex-plugin/plugin.json"
if ($null -ne $plugin) {
    Assert-True ($plugin.name -eq "meraki-codex-ops") "plugin name is meraki-codex-ops"
    Assert-True ($plugin.mcpServers -eq "./.mcp.json") "plugin mcpServers points to ./.mcp.json"

    if ($plugin.PSObject.Properties.Name -contains "skills") {
        $skillsPath = Resolve-PluginRelativePath $plugin.skills
        Assert-True (Test-Path $skillsPath) "plugin skills path exists when declared"
    }

    $mcpPath = Resolve-PluginRelativePath $plugin.mcpServers
    Assert-True (Test-Path $mcpPath) "plugin mcpServers path exists"
}

$mcp = Read-JsonFile ".mcp.json"
if ($null -ne $mcp) {
    Assert-True ($null -ne $mcp.mcpServers) ".mcp.json has mcpServers"
    $mcpServerNames = @()
    if ($null -ne $mcp.mcpServers) {
        $mcpServerNames = $mcp.mcpServers.PSObject.Properties.Name
    }

    foreach ($server in $expectedMcpServers) {
        Assert-True ($mcpServerNames -contains $server) ".mcp.json defines $server"
    }

    $mcpJsonText = Get-Content -Raw (Join-Path $root ".mcp.json")
    Assert-True (-not [regex]::IsMatch($mcpJsonText, "YOUR_|ghp_|sk-|xox[baprs]-")) ".mcp.json contains no obvious placeholder or token"
}

$wpCommand = Get-Command wp -ErrorAction SilentlyContinue
if ($null -eq $wpCommand) {
    Write-Host "Warning: wp was not found on PATH; wordpress-local is configured but will not start until WP-CLI is available." -ForegroundColor Yellow
}

if ($failures.Count -gt 0) {
    Write-Host "Codex asset validation failed:" -ForegroundColor Red
    foreach ($failure in $failures) {
        Write-Host " - $failure" -ForegroundColor Red
    }
    exit 1
}

Write-Host "Codex asset validation passed." -ForegroundColor Green
