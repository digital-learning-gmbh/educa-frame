<?php
// config for DigitalLearning/EducaWorkflow

return [

    "processor" => "sync", // sync or job

    "blocks" => [

        // educa Blocks
        \App\Http\Workflow\Blocks\EducaCreationBlock::class,
        \DigitalLearning\EducaWorkflow\Blocks\EndBlock::class,
        \DigitalLearning\EducaWorkflow\Blocks\MailBlock::class,
        \DigitalLearning\EducaWorkflow\Blocks\TimerBlock::class,
        \App\Http\Workflow\Blocks\VariableSwitcher::class,

        \App\Http\Workflow\Blocks\FeedCardCreationBlock::class,
        \App\Http\Workflow\Blocks\GroupCreationBlock::class,
        \App\Http\Workflow\Blocks\GroupMemberEditBlock::class,

        \App\Http\Workflow\Blocks\AddSkillBlock::class,
    ],

    "log_level" => "production"
];
