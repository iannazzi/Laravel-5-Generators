<?php

namespace Iannazzi\Generators\Migrations;

class SchemaParser
{
    /**
     * The parsed schema.
     *
     * @var array
     */
    private $schema = [];

    function parseFields($fields)
    {

        foreach ($fields as $field)
        {
            $parsed_field = [];
            $parsed_field['name'] = $field['field'];
             $parsed_field['type'] = $field['type'];
             $parsed_field['arguments'] = (isset($field['args'])) ? [$field['args']] : [];

            //parseArguments(&$type)


            $options = (isset($field['decorators'])) ? $field['decorators'] : [];

            $parsed_field['options'] =  $this->parseOptions($options);
            $this->addField($parsed_field);
        }
        return $this->schema;
    }

    /**
     * Parse the command line migration schema.
     * Ex: name:string, age:integer:nullable
     *
     * @param  string $schema
     * @return array
     */
    public function parseSchema($schema)
    {
        $fields = $this->splitIntoFields($schema);
        foreach ($fields as $field)
        {
            $segments = $this->parseSegments($field);

            if ($this->fieldNeedsForeignConstraint($segments))
            {
                unset($segments['options']['foreign']);

                // If the user wants a foreign constraint, then
                // we'll first add the regular field.
                $this->addField($segments);

                // And then add another field for the constraint.
                $this->addForeignConstraint($segments);

                continue;
            }
            $this->addField($segments);

        }

        return $this->schema;
    }

    /**
     * Add a field to the schema array.
     *
     * @param  array $field
     * @return $this
     */
    private function addField($field)
    {
        $this->schema[] = $field;

        return $this;
    }

    /**
     * Get an array of fields from the given schema.
     *
     * @param  string $schema
     * @return array
     */
    private function splitIntoFields($schema)
    {
        return preg_split('/,\s?(?![^()]*\))/', $schema);
    }

    /**
     * Get the segments of the schema field.
     *
     * @param  string $field
     * @return array
     */
    private function parseSegments($field)
    {
        $segments = explode(':', $field);
        $name = array_shift($segments);
        $type = array_shift($segments);
        var_dump($type);
        $options = $this->parseOptions($segments);


        $arguments = $this->parseArguments($type);

        return compact('name', 'type', 'arguments', 'options');
    }
    private function parseArguments($type)
    {
        $arguments = [];
        // Do we have arguments being used here?
        // Like: string(100)
        if (preg_match('/(.+?)\(([^)]+)\)/', $type, $matches))
        {
            $type = $matches[1];
            $arguments = explode(',', $matches[2]);
        }
        return $arguments;

    }

    /**
     * Parse any given options into something usable.
     *
     * @param  array $options
     * @return array
     */
    private function parseOptions($options)
    {
        if (empty($options)) return [];

        foreach ($options as $option)
        {
            if (str_contains($option, '('))
            {
                preg_match('/([a-z]+)\(([^\)]+)\)/i', $option, $matches);

                $results[ $matches[1] ] = $matches[2];
            } else
            {
                $results[ $option ] = true;
            }
        }

        return $results;
    }

    /**
     * Add a foreign constraint field to the schema.
     *
     * @param array $segments
     */
    private function addForeignConstraint($segments)
    {
        $string = sprintf(
            "%s:foreign:references('id'):on('%s')",
            $segments['name'],
            $this->getTableNameFromForeignKey($segments['name'])
        );

        $this->addField($this->parseSegments($string));
    }

    /**
     * Try to figure out the name of a table from a foreign key.
     * Ex: user_id => users
     *
     * @param  string $key
     * @return string
     */
    private function getTableNameFromForeignKey($key)
    {
        return str_plural(str_replace('_id', '', $key));
    }

    /**
     * Determine if the user wants a foreign constraint for the field.
     *
     * @param  array $segments
     * @return bool
     */
    private function fieldNeedsForeignConstraint($segments)
    {
        return array_key_exists('foreign', $segments['options']);
    }
}

