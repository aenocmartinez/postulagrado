<?php

namespace Src\shared\response;

class ResponsePostulaGrado
{
    private int $code;
    private string $message;
    private $data;

    public function __construct($code = 200, $message = "", $data = [])
    {
        $this->code = $code;
        $this->message = $message;
        $this->data = [];
    }

    public function setCode(int $code): void {
        $this->code = $code;
    }

    public function getCode(): int {
        return $this->code;
    }

    public function setMessage(string $message): void {
        $this->message = $message;
    }

    public function getMessage(): string {
        return $this->message;
    }

    public function setData($data): void {
        $this->data = $data;
    }

    public function getData(){
        return $this->data;
    }
}