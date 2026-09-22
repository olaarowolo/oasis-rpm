<?php

return [
    /*
    |--------------------------------------------------------------------------
    | 12-Stage Research Lifecycle Configuration
    |--------------------------------------------------------------------------
    | Defines all 12 research stages with metadata for tracking progression
    |
    */
    'stages' => [
        [
            'number' => 1,
            'name' => 'Topic Ideation & Approval',
            'description' => 'Student proposes research topic for supervisor approval',
            'key_deliverables' => ['Research Topic Proposal', 'Topic Abstract'],
            'prerequisites' => [],
            'estimated_duration_weeks' => 2,
            'resources_gate' => [],
        ],
        [
            'number' => 2,
            'name' => 'Research Gap Identification',
            'description' => 'Identify the research gap and justify the study',
            'key_deliverables' => ['Gap Analysis', 'Research Justification'],
            'prerequisites' => ['Topic Approval'],
            'estimated_duration_weeks' => 3,
            'resources_gate' => ['background_resources'],
        ],
        [
            'number' => 3,
            'name' => 'Chapter 1 - Introduction',
            'description' => 'Write comprehensive introduction and contextualization',
            'key_deliverables' => ['Introduction Chapter', 'Problem Statement'],
            'prerequisites' => ['Research Gap Identification'],
            'estimated_duration_weeks' => 2,
            'resources_gate' => ['chapter1_resources'],
        ],
        [
            'number' => 4,
            'name' => 'Chapter 2 - Literature Review',
            'description' => 'Conduct comprehensive literature review',
            'key_deliverables' => ['Literature Review Chapter', 'Annotated Bibliography'],
            'prerequisites' => ['Chapter 1 Completion'],
            'estimated_duration_weeks' => 4,
            'resources_gate' => ['literature_resources'],
        ],
        [
            'number' => 5,
            'name' => 'Chapter 3 - Methodology',
            'description' => 'Design and document research methodology',
            'key_deliverables' => ['Methodology Chapter', 'Research Design Document'],
            'prerequisites' => ['Literature Review Completion'],
            'estimated_duration_weeks' => 3,
            'resources_gate' => ['methodology_resources'],
        ],
        [
            'number' => 6,
            'name' => 'Chapter 4 - Data Analysis',
            'description' => 'Collect and analyze research data',
            'key_deliverables' => ['Data Analysis Chapter', 'Results Summary'],
            'prerequisites' => ['Methodology Completion'],
            'estimated_duration_weeks' => 4,
            'resources_gate' => ['data_analysis_resources'],
        ],
        [
            'number' => 7,
            'name' => 'Chapter 5 - Conclusion',
            'description' => 'Write findings, discussion, and conclusions',
            'key_deliverables' => ['Conclusion Chapter', 'Key Findings'],
            'prerequisites' => ['Data Analysis Completion'],
            'estimated_duration_weeks' => 3,
            'resources_gate' => ['conclusion_resources'],
        ],
        [
            'number' => 8,
            'name' => 'Supervisor Review & Feedback',
            'description' => 'Receive comprehensive supervisor feedback',
            'key_deliverables' => ['Feedback Summary', 'Revision Plan'],
            'prerequisites' => ['Conclusion Completion'],
            'estimated_duration_weeks' => 1,
            'resources_gate' => [],
        ],
        [
            'number' => 9,
            'name' => 'Revisions & Final Edits',
            'description' => 'Implement revisions and finalize document',
            'key_deliverables' => ['Revised Thesis', 'Final Edits Summary'],
            'prerequisites' => ['Supervisor Review'],
            'estimated_duration_weeks' => 2,
            'resources_gate' => ['editing_resources'],
        ],
        [
            'number' => 10,
            'name' => 'Defense Preparation',
            'description' => 'Prepare presentation and defense materials',
            'key_deliverables' => ['Defense Presentation', 'Q&A Preparation'],
            'prerequisites' => ['Final Edits'],
            'estimated_duration_weeks' => 1,
            'resources_gate' => ['defense_resources'],
        ],
        [
            'number' => 11,
            'name' => 'Project/Thesis Defense',
            'description' => 'Defend project/thesis before the panel',
            'key_deliverables' => ['Defense Presentation', 'Defense Outcome'],
            'prerequisites' => ['Defense Preparation'],
            'estimated_duration_weeks' => 1,
            'resources_gate' => [],
        ],
        [
            'number' => 12,
            'name' => 'Project Completion (Sign-off/Graduation)',
            'description' => 'Finalize sign-off, graduation status, and archive handoff',
            'key_deliverables' => ['Final Approved Manuscript', 'Graduation Sign-off'],
            'prerequisites' => ['Project/Thesis Defense'],
            'estimated_duration_weeks' => 0,
            'resources_gate' => [],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stage Advancement Rules
    |--------------------------------------------------------------------------
    | Rules for when students can advance between stages
    |
    */
    'advancement_rules' => [
        'require_topic_approval' => true,
        'require_resource_completion' => true,
        'require_meeting_log' => false,
        'min_point_percentage' => 70,
    ],

    /*
    |--------------------------------------------------------------------------
    | Point System
    |--------------------------------------------------------------------------
    | Points earned through resource completion
    |
    */
    'points' => [
        'max_per_resource' => 25,
        'bonus_for_early_completion' => 5,
        'total_possible_points' => 500,
    ],
];
