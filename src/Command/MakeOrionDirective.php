<?php

namespace Orion\OrionEngine\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'orion:make:directive',
    description: 'Create directive'
)]
class MakeOrionDirective extends Command
{
    private string $file;

    public function __construct(string $file)
    {
        $this->file = $file;
        parent::__construct();
    }

    protected function configure()
    {
        $this->addOption('valor', null, InputOption::VALUE_REQUIRED, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if(!file_exists($this->file)){
            $io->error('Not found directive file.');
            return Command::FAILURE;
        }

        $directive_name = $io->ask("Directive Name");

        if (empty($directive_name)) {
            $io->error('Directive name cannot be empty.');
            return Command::FAILURE;
        }

        // Validar se o nome da directive é válido (apenas letras, números e underscore)
        if (!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $directive_name)) {
            $io->error('Invalid directive name. Use only letters, numbers and underscores.');
            return Command::FAILURE;
        }

        // Ler o conteúdo atual do arquivo
        $currentContent = file_get_contents($this->file);

        // Verificar se a directive já existe
        if (strpos($currentContent, "OrionSymfony::directive('$directive_name'") !== false) {
            $io->error("Directive '$directive_name' already exists.");
            return Command::FAILURE;
        }

        // Criar a nova directive
        $newDirective = "\nOrionSymfony::directive('$directive_name', function (\$expression) {\n    return '<?php  ?>';\n});";

        // Encontrar a posição do return statement
        $returnPosition = strrpos($currentContent, 'return OrionSymfony::getAllDirectives();');

        if ($returnPosition === false) {
            $io->error('Invalid directive file format. Could not find return statement.');
            return Command::FAILURE;
        }

        // Inserir a nova directive antes do return
        $newContent = substr_replace($currentContent, $newDirective . "\n\n", $returnPosition, 0);

        // Escrever o novo conteúdo no arquivo
        if (file_put_contents($this->file, $newContent) === false) {
            $io->error('Failed to write to directive file.');
            return Command::FAILURE;
        }

        $io->success("Directive '$directive_name' created successfully!");
        $io->note("Don't forget to implement the logic inside the directive function.");

        return Command::SUCCESS;
    }
}