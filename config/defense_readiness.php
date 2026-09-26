<?php

/*
|--------------------------------------------------------------------------
| Defense Readiness Manuscript Form Configuration
|--------------------------------------------------------------------------
| Defines the ordered section catalogue, word-count targets, and guidance
| text for the defense-readiness manuscript form. Sections are seeded from
| this config when a student's document is first created.
*/

return [

    /*
    |--------------------------------------------------------------------------
    | Top-level sections
    |--------------------------------------------------------------------------
    | Ordered top-level sections. Each section has a stable `key`, a display
    | `title`, word-count targets, and `guidance` shown in the helper panel.
    | The `methodology` entry is a group whose children live below.
    */

    'sections' => [
        [
            'key' => 'title_abstract',
            'title' => 'Title, Abstract and Keywords',
            'target_min_words' => 250,
            'target_max_words' => 300,
            'word_count_label' => null,
            'guidance' => 'Write a clear, concise abstract as a single paragraph of about 180–250 words. It should cover: the background or problem, the purpose of the study, the theoretical framework, the method, the sample, the key findings, the conclusion, and the recommendation. The title and keywords should be entered at the top of the editor.',
        ],
        [
            'key' => 'introduction',
            'title' => 'Introduction',
            'target_min_words' => 700,
            'target_max_words' => 900,
            'word_count_label' => null,
            'guidance' => 'Introduce the research problem, state the objectives, and outline the structure of the chapter.',
        ],
        [
            'key' => 'literature_review',
            'title' => 'Literature Review and Theory',
            'target_min_words' => 1000,
            'target_max_words' => 1300,
            'word_count_label' => null,
            'guidance' => 'Survey the existing body of knowledge relevant to your research problem and identify the gap your study addresses.',
        ],
        [
            'key' => 'methodology',
            'title' => 'Methodology',
            'is_group' => true,
            'target_min_words' => 700,
            'target_max_words' => 900,
            'word_count_label' => null,
            'guidance' => 'This section is organised into methodological sub-sections, each reviewed independently.',
        ],
        [
            'key' => 'findings',
            'title' => 'Findings',
            'target_min_words' => 900,
            'target_max_words' => 1200,
            'word_count_label' => null,
            'guidance' => 'Present your research findings in a clear, logical order, supported by tables, figures, and quotes where appropriate.',
        ],
        [
            'key' => 'discussion',
            'title' => 'Discussion',
            'target_min_words' => 900,
            'target_max_words' => 1100,
            'word_count_label' => null,
            'guidance' => 'Interpret your findings in relation to the literature and research questions, discussing implications and limitations.',
        ],
        [
            'key' => 'conclusion',
            'title' => 'Conclusion and Recommendations',
            'target_min_words' => 500,
            'target_max_words' => 700,
            'word_count_label' => null,
            'guidance' => 'Summarise your key findings, state the main conclusion, and offer recommendations for further research and practice.',
        ],
        [
            'key' => 'references',
            'title' => 'References',
            'target_min_words' => null,
            'target_max_words' => null,
            'word_count_label' => 'As required',
            'guidance' => 'List all sources cited in the manuscript, formatted consistently according to your department\'s style guide.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Methodology children
    |--------------------------------------------------------------------------
    | Methodology is a group section. Each child has its own status and review
    | cycle. Children 4, 5, and 6 have approach-specific guidance text.
    | The ethics child is only created when primary_data_collection is true.
    */

    'methodology_children' => [
        [
            'position' => 1,
            'key' => 'methodology_design',
            'title' => 'Research design',
            'guidance' => 'Describe the overall research strategy and design (e.g. experimental, quasi-experimental, ethnographic, case study).',
        ],
        [
            'position' => 2,
            'key' => 'methodology_population',
            'title' => 'Study population and setting',
            'guidance' => 'Define the target population, the setting of the study, and the inclusion/exclusion criteria.',
        ],
        [
            'position' => 3,
            'key' => 'methodology_sampling',
            'title' => 'Sampling procedure and sample size',
            'guidance' => 'Explain how participants were selected and justify the sample size.',
        ],
        [
            'position' => 4,
            'key' => 'methodology_instrument',
            'title' => 'Instrument',
            'guidance' => [
                'default' => 'Describe the instrument used for data collection and its validation process.',
                'quantitative' => 'Describe the instrument used for data collection, including its psychometric properties, validity, and reliability.',
                'qualitative' => 'Describe the instrument or protocol used for data collection, including its development and piloting process.',
                'mixed' => 'Describe the instruments used for both quantitative and qualitative data collection, including their validation.',
            ],
        ],
        [
            'position' => 5,
            'key' => 'methodology_data_collection',
            'title' => 'Data collection',
            'guidance' => [
                'default' => 'Explain the procedures followed during data collection.',
                'quantitative' => 'Explain the quantitative data collection procedures, including any standardised instruments and protocols.',
                'qualitative' => 'Explain the qualitative data collection procedures, including the mode of data gathering (interviews, focus groups, observation).',
                'mixed' => 'Explain the procedures for collecting both quantitative and qualitative data, including any sequencing or integration decisions.',
            ],
        ],
        [
            'position' => 6,
            'key' => 'methodology_data_analysis',
            'title' => 'Data analysis',
            'guidance' => [
                'default' => 'Describe the steps taken to analyse the data.',
                'quantitative' => 'Describe the statistical methods used to analyse quantitative data (e.g. descriptive and inferential statistics).',
                'qualitative' => 'Describe the methods used to analyse qualitative data (e.g. thematic, narrative, or grounded theory analysis).',
                'mixed' => 'Describe the methods used to analyse both quantitative and qualitative data, and how the two are integrated.',
            ],
        ],
        [
            'position' => 7,
            'key' => 'methodology_ethics',
            'title' => 'Ethical considerations',
            'guidance' => 'Detail the ethical approvals obtained, consent procedures, confidentiality measures, and any risks mitigated.',
            'conditional_on' => 'primary_data_collection',
        ],
        [
            'position' => 8,
            'key' => 'methodology_limitations',
            'title' => 'Limitations',
            'guidance' => 'Acknowledge the limitations of the study and how they may affect the interpretation of findings.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Display helpers
    |--------------------------------------------------------------------------
    */

    'status_labels' => [
        'draft' => 'Draft',
        'in_review' => 'Awaiting Review',
        'in_revision' => 'Revision Requested',
        'halted' => 'Halted',
        'completed' => 'Complete',
    ],

    'section_status_labels' => [
        'locked' => 'Locked',
        'draft' => 'Draft',
        'submitted' => 'In Review',
        'accepted' => 'Accepted',
        'conditional' => 'Conditionally Approved',
        'revision_requested' => 'Revision Requested',
        'rejected' => 'Rejected',
    ],
];
