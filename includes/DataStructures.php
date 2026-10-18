<?php

class Queue
{
    private array $items = [];
    public function enqueue(array $item): void { $this->items[] = $item; }
    public function dequeue(): ?array { return $this->isEmpty() ? null : array_shift($this->items); }
    public function isEmpty(): bool { return empty($this->items); }
    public function toArray(): array { return $this->items; }
}

class Stack
{
    private array $items = [];
    public function push(array $item): void { $this->items[] = $item; }
    public function pop(): ?array { return $this->isEmpty() ? null : array_pop($this->items); }
    public function isEmpty(): bool { return empty($this->items); }
    public function toArray(): array { return array_reverse($this->items); }
}