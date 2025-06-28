<?php 
namespace TaskList\Service;

interface NotifierInterface
{
    public function notify(): array;
}