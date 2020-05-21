<?php

namespace App;

use Google_Client;
use Google_Service_Tasks;
use Google_Service_Tasks_Task;
use Google_Service_Tasks_TaskList;

class TaskClient
{
    public $client;
    public $service;
    public $default;
    public function __construct()
    {
        $this->client = $this->getClient();
        $this->service = new Google_Service_Tasks($this->client);
        $this->default = $this->getDefaultTasklist();
    }
    public function getClient()
    {
        $client = new Google_Client();
        $client->setApplicationName('Eat');
        $client->setScopes([Google_Service_Tasks::TASKS_READONLY, Google_Service_Tasks::TASKS]);
        $client->setAuthConfig(base_path('service-account.json'));
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');
        return $client;
    }
    public function getDefaultTaskList(): Google_Service_Tasks_TaskList
    {
        $taskLists = $this->service->tasklists->listTasklists();
        foreach ($taskLists->getItems() as $taskList) {
            if ($taskList->getTitle() == 'default') {
                return $taskList;
            }
        }
        if (!$this->default) {
            $tl = new Google_Service_Tasks_TaskList();
            $tl->setTitle("default");
            return $this->service->tasklists->insert($tl);
        }
    }
    public function addTask($title, $note = null, $date = null)
    {
        $task = new Google_Service_Tasks_Task();
        $task->setTitle($title);
        $task->setNotes($note ?: '');
        $result = $this->service->tasks->insert($this->default->id, $task);
        return $result;
    }
    public function updateTask($id, $title, $notes = null, $date = null)
    {
        $task = $this->getTask($id);
        $task->setTitle($title);
        $task->setNotes($notes);
        return $this->service->tasks->update($this->default->id, $id, $task);
    }
    public function deleteTask($id)
    {
        $this->service->tasks->delete($this->default->id, $id);
        return $id;
    }
    public function getTask($id)
    {
        return $this->service->tasks->get($this->default->id, $id);
    }
}
