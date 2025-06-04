<?php

namespace Orion\OrionEngine\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'orion:make:function',
    description: 'Create function in selected file'
)]
class MakeOrionFunction extends Command
{
    private string $dir;

    public function __construct(string $dir)
    {
        $this->dir = $dir;
        parent::__construct();
    }

    protected function configure()
    {
        $this->addOption('valor', null, InputOption::VALUE_REQUIRED, 'Option description');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if(!file_exists($this->dir)){
            $io->error('Not found functions directory and php files.');
            return Command::FAILURE;
        }

        $files = scandir($this->dir);
        $files = array_values(array_diff(scandir($this->dir), ['.', '..']));

        if(count($files) === 0){
            $io->error('Not found php files.');
            return Command::FAILURE;
        }

        $io->writeln("Escolha uma Opção");

        foreach($files as $index => $value){
            $io->writeln($index.".".$value);
        }

        $option = (int)$io->ask("Option");

        // Validar se a opção existe
        if(!isset($files[$option])){
            $io->error('Invalid option selected.');
            return Command::FAILURE;
        }

        $file_name = $files[$option];
        $file_path = $this->dir.DIRECTORY_SEPARATOR.$file_name;

        $io->writeln($file_name);

        if(!file_exists($file_path)){
            $io->error('Not found file.');
            return Command::FAILURE;
        }

        $name = $io->ask('Function Name');

        // Validar se o nome da função foi fornecido
        if(empty($name)){
            $io->error('Function name is required.');
            return Command::FAILURE;
        }

        // Validar se o nome da função é válido (apenas letras, números e underscore)
        if(!preg_match('/^[a-zA-Z_][a-zA-Z0-9_]*$/', $name)){
            $io->error('Invalid function name. Use only letters, numbers and underscore.');
            return Command::FAILURE;
        }

        try {
            // Verificar se a função já existe em TODOS os arquivos do diretório
            $functionExists = $this->checkFunctionExistsInAllFiles($name, $files);
            
            if($functionExists !== false){
                $io->error("Function '$name' already exists in file: '{$functionExists}'.");
                return Command::FAILURE;
            }

            // Ler o conteúdo atual do arquivo
            $currentContent = file_get_contents($file_path);

            // Criar a nova função
            $newFunction = "\n\nfunction $name(){\n\n}\n";

            if(substr(trim($currentContent), -2) === '?>'){
                $newContent = substr($currentContent, 0, strrpos($currentContent, '?>')) . $newFunction . '?>';
            } else {
                // Caso contrário, apenas adicionar no final
                $newContent = $currentContent . $newFunction;
            }

            // Escrever o novo conteúdo no arquivo
            if(file_put_contents($file_path, $newContent) !== false){
                $io->success("Function '$name' successfully added to '$file_name'.");
            } else {
                $io->error('Failed to write to file.');
                return Command::FAILURE;
            }

        } catch(\Exception $e){
            $io->error('Error processing file: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Verifica se a função já existe em todos os arquivos do diretório
     * 
     * @param string $functionName Nome da função a ser verificada
     * @param array $files Array com os nomes dos arquivos
     * @return string|false Retorna o nome do arquivo onde a função existe, ou false se não existir
     */
    private function checkFunctionExistsInAllFiles(string $functionName, array $files)
    {
        foreach($files as $file){
            $filePath = $this->dir . DIRECTORY_SEPARATOR . $file;
            
            // Verificar se é um arquivo PHP
            if(pathinfo($file, PATHINFO_EXTENSION) !== 'php'){
                continue;
            }
            
            if(file_exists($filePath)){
                $content = file_get_contents($filePath);
                
                // Verificar se a função existe no arquivo atual
                // Usa regex mais robusta para detectar a função
                if(preg_match('/function\s+' . preg_quote($functionName, '/') . '\s*\(/i', $content)){
                    return $file;
                }
            }
        }
        
        return false;
    }
}