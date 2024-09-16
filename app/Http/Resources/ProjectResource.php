<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{

    private array $requiredRelations = [
//        'assignees'
    ];

    //
    public function toArray(Request $request): array
    {
        $this->checkRequiredRelations();

        return [
            'project_id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'assignees' => ['Bob Dylan']
//            'assignees' => $this->whenLoaded('assignees');
        ];
    }

    //
    private function checkRequiredRelations(): void
    {
        foreach ($this->requiredRelations as $requiredRelation) {
            $this->load($requiredRelation);
        }
    }
}
