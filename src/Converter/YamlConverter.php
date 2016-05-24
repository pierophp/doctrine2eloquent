<?php

namespace Doctrine2Eloquent\Converter;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class YamlConverter
{
    protected $yamlPath;

    protected $modelPath;

    protected $modelNamespace;

    /**
     * @return mixed
     */
    public function getYamlPath()
    {
        return $this->yamlPath;
    }

    /**
     * @param mixed $yamlPath
     */
    public function setYamlPath($yamlPath)
    {
        $this->yamlPath = $yamlPath;
    }

    /**
     * @return mixed
     */
    public function getModelPath()
    {
        return $this->modelPath;
    }

    /**
     * @param mixed $modelPath
     */
    public function setModelPath($modelPath)
    {
        $this->modelPath = $modelPath;
    }

    /**
     * @return mixed
     */
    public function getModelNamespace()
    {
        return $this->modelNamespace;
    }

    /**
     * @param mixed $modelNamespace
     */
    public function setModelNamespace($modelNamespace)
    {
        $this->modelNamespace = $modelNamespace;
    }


    public function convert()
    {
        $finder = new Finder();
        $finder->files()->in($this->getYamlPath());
        foreach ($finder as $file) {
            $this->convertFile($file->getRealpath());
        }
    }

    public function convertFile($yamlFile)
    {
        $entity = Yaml::parse(file_get_contents($yamlFile));
        $entityKey = array_keys($entity)[0];

        $attr = $entity[$entityKey];

        if (!isset($attr['manyToOne'])) {
            $attr['manyToOne'] = array();
        }

        if (!isset($attr['oneToOne'])) {
            $attr['oneToOne'] = array();
        }

        if (!isset($attr['oneToMany'])) {
            $attr['oneToMany'] = array();
        }

        if (!isset($attr['manyToMany'])) {
            $attr['manyToMany'] = array();
        }

        $entityName = explode('\\', $entityKey);
        $entityName = $entityName[count($entityName) - 1];

        $this->generateBaseFile($entityName, $attr);
        $this->generateExtendsFile($entityName, $attr);
    }

    protected function convertCamelCaseToUnderline($string)
    {
        preg_match_all('/((?:^|[A-Z])[a-z]+)/', $string, $matches);

        if (!isset($matches[0])) {
            return $string;
        }

        $response = strtolower(implode('_', $matches[0]));

        return $response;
    }

    protected function generateBaseFile($entityName, $attr)
    {
        $fileContent = "<?php\n\n";
        $fileContent .= "namespace {$this->getModelNamespace()}\\Base;\n\n";
        $fileContent .= "use Illuminate\\Database\\Eloquent\\Model;\n\n";
        $fileContent .= "/*\n";
        $fileContent .= " * AUTO GENERATED - DO NOT CHANGE HERE\n";
        $fileContent .= " * */\n\n";
        $fileContent .= "class Base{$entityName} extends Model\n";
        $fileContent .= "{\n\n";
        $fileContent .= "    protected \$table = '{$attr['table']}';\n\n";

        foreach ($attr['manyToOne'] as $manyToOneKey => $manyToOne) {

            $targetEntity = $manyToOne['targetEntity'];

            $fileContent .= "    public function {$manyToOneKey}()\n";
            $fileContent .= "    {\n";
            $fileContent .= "        return \$this->belongsTo('{$this->getModelNamespace()}\\{$targetEntity}');\n";
            $fileContent .= "    }\n\n";

        }

        foreach ($attr['oneToOne'] as $oneToOneKey => $oneToOne) {

            $targetEntity = $oneToOne['targetEntity'];

            $fileContent .= "    public function {$oneToOneKey}()\n";
            $fileContent .= "    {\n";
            $fileContent .= "        return \$this->belongsTo'{$this->getModelNamespace()}\\{$targetEntity}');\n";
            $fileContent .= "    }\n\n";

        }

        foreach ($attr['oneToMany'] as $oneToManyKey => $oneToMany) {

            $targetEntity = $oneToMany['targetEntity'];

            $fileContent .= "    public function {$oneToManyKey}()\n";
            $fileContent .= "    {\n";
            $fileContent .= "        return \$this->hasMany('{$this->getModelNamespace()}\\{$targetEntity}');\n";
            $fileContent .= "    }\n\n";
        }

        foreach ($attr['manyToMany'] as $manyToManyKey => $manyToMany) {

            $targetEntity = $manyToMany['targetEntity'];

            if (!isset($manyToMany['joinTable']['name'])) {
                $tableName = $this->convertCamelCaseToUnderline($targetEntity) . '_has_' . $manyToMany['mappedBy'];
            } else {
                $tableName = $manyToMany['joinTable']['name'];
            }

            $fileContent .= "    public function {$manyToManyKey}()\n";
            $fileContent .= "    {\n";
            $fileContent .= "        return \$this->belongsToMany('{$this->getModelNamespace()}\\{$targetEntity}', '{$tableName}');\n";
            $fileContent .= "    }\n\n";
        }

        $fileContent .= "}";

        $dirPath = $this->getModelPath() . DIRECTORY_SEPARATOR . 'Base' . DIRECTORY_SEPARATOR;

        if (!file_exists($dirPath)) {
            mkdir($dirPath);
        }

        $modelPath = $dirPath . $entityName . '.php';

        file_put_contents($modelPath, $fileContent);
    }

    protected function generateExtendsFile($entityName, $attr)
    {
        $fileContent = "<?php\n\n";
        $fileContent .= "namespace {$this->getModelNamespace()};\n\n";
        $fileContent .= "use {$this->getModelNamespace()}\\Base\\Base{$entityName};\n\n";
        $fileContent .= "class {$entityName} extends Base{$entityName}\n";
        $fileContent .= "{\n\n";
        $fileContent .= "}";

        $modelPath = $this->getModelPath() . DIRECTORY_SEPARATOR . $entityName . '.php';
        if (file_exists($modelPath)) {
            return;
        }

        file_put_contents($modelPath, $fileContent);
    }
}

