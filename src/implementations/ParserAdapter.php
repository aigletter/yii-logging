<?php

namespace aigletter\logging\implementations;

use aigletter\logging\contracts\ParserInterface;
use aigletter\logging\dto\LogDto;
use Kassner\LogParser\LogParser;

class ParserAdapter implements ParserInterface
{
    protected $parser;

    protected function getFieldMap()
    {
        return [
            // %a - Remote IP-address
            'remoteIp' => 'remoteAddr',
            // %u - Remote user (from auth; may be bogus if return status (%s) is 401)
            'user' => 'remoteUser',
            // %t - Time the request was received (standard english format)
            'time' => [
                'property' => 'timeLocal',
                'format' => 'DateTime',
            ],
            // %r - First line of request
            'request' => 'request',
            // %>s - status
            'status' => 'status',
            // %O - Bytes sent, including headers, cannot be zero. You need to enable mod_logio to use this.
            'sentBytes' => 'bodyBytesSent',
            // %{Foobar}i -  - The contents of Foobar: header line(s) in the request sent to the server.
            'HeaderReferer' => 'httpReferer',
            // %{Foobar}i -  - The contents of Foobar: header line(s) in the request sent to the server.
            'HeaderUserAgent' => 'httpUserAgent',
        ];
    }

    public function __construct(string $format)
    {
        $this->parser = new LogParser($format);
    }

    public function parse(string $line): LogDto
    {
        $result = $this->parser->parse($line);

        return $this->map($result);
    }

    protected function map(object $result): LogDto
    {
        $dto = new LogDto();
        $fieldMap = $this->getFieldMap();
        foreach ($result as $key => $value) {
            if (array_key_exists($key, $fieldMap)) {
                $property = $fieldMap[$key];
                if (is_array($property)) {
                    if (isset($property['format'])) {
                        $value = $this->format($value, $property['format']);
                    }
                    $property = $property['property'];
                }
                $dto->{$property} = $value;
            }
        }

        return $dto;
    }

    protected function format($value, $type)
    {
        if (is_callable($type)) {
            return $type($value);
        }

        if (in_array($type, ["bool", "boolean", "int", "integer", "float", "double", "string", "array", "object", "null"])) {
            settype($value, $type);
            return $value;
        }

        $formatter = 'format' . $type;
        if (method_exists($this, $formatter)) {
            return $this->{$formatter}($value);
        }

        return $value;
    }

    protected function formatDateTime($value)
    {
        return (new \DateTime($value))->format('Y-m-d H:i:s.v');
    }
}