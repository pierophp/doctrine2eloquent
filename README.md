# Doctrine2Eloquent

Created to convert YML doctrine to eloquent models

## Usage
```
app/console doctrine2eloquent:yamlconverter --yaml_path src/AcmeBundle/Resources/config/doctrine --model_path src/AcmeBundle/Model --model_namespace AcmeBundle\\Model
```

## Params
### --yaml_path
Required. The YML path of doctrine definitions

### --model_path
Required. The path of the Eloquent models to be generated

### --model_namespace
Required. The namespace of Eloquent models to be generated

### --model_prefix
Optional. The prefix of Eloquent models to be generated
