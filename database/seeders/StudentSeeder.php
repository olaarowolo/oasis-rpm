<?php

namespace Database\Seeders;

use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        // Clear existing students
        Student::truncate();

        // ============ NEW STUDENTS FOR DR. OLAAROWOLO (olaarowolo.ng@gmail.com) ============
        // Supervisor: Dr. Olasunkanmi Arowolo (olaarowolo.ng@gmail.com)
        // Department: Journalism & Media Studies
        
        $arowoloStudents = [
            [
                'full_name' => 'Saliu Halima Opemipo',
                'matric_number' => '230910152',
                'email' => 'halima.saliu230910152@st.lasu.edu.ng',
                'phone' => '08128132711',
                'degree_level' => '400 Level',
                'research_topic' => null,
                'current_stage' => 1,
                'progress_percentage' => 0,
            ],
            [
                'full_name' => 'Emmanuella Oluwatofunmi Oladejo',
                'matric_number' => '230910239',
                'email' => 'emmanuella.oladejo230910239@st.lasu.edu.ng',
                'phone' => '09164072585',
                'degree_level' => '400 Level',
                'research_topic' => null,
                'current_stage' => 1,
                'progress_percentage' => 0,
            ],
            [
                'full_name' => 'Fatai Oluwatobiloba Iyioluwa',
                'matric_number' => '230910066',
                'email' => 'Oluwatobiloba.fatai230910066@st.lasu.edu.ng',
                'phone' => '08038708931',
                'degree_level' => '400 Level',
                'research_topic' => null,
                'current_stage' => 1,
                'progress_percentage' => 0,
            ],
            [
                'full_name' => 'Odetayo David Dominion',
                'matric_number' => '230910232',
                'email' => 'david.odetayo230910232@st.lasu.edu.ng',
                'phone' => '07081383981',
                'degree_level' => '400 Level',
                'research_topic' => null,
                'current_stage' => 1,
                'progress_percentage' => 0,
            ],
            [
                'full_name' => 'Agbaje Ebunoluwa Abigeal',
                'matric_number' => '230910016',
                'email' => 'ebunoluwa.agbaje230910016@st.lasu.edu.ng',
                'phone' => '07045235381',
                'degree_level' => '400 Level',
                'research_topic' => 'Artificial intelligence in radio broadcasting: A comparative study of Audience perception of Ai generated and human voices in podcast and radio presentation.',
                'current_stage' => 2,
                'progress_percentage' => 10,
            ],
            [
                'full_name' => 'Ifeoluwa Victor Lawal',
                'matric_number' => '230910223',
                'email' => 'Ifeoluwa.lawal230910223@st.lasu.edu.ng',
                'phone' => '09114242324',
                'degree_level' => '400 Level',
                'research_topic' => 'The Impact of Video Editing on Audience Engagement on TikTok among LASU Undergraduates',
                'current_stage' => 1,
                'progress_percentage' => 0,
            ],
            [
                'full_name' => 'Onaara Oluwafayokemi Oluferranmi',
                'matric_number' => '230910135',
                'email' => 'oluwafayokemi.onaara230910135@st.lasu.edu.ng',
                'phone' => '09128548035',
                'degree_level' => '400 Level',
                'research_topic' => null,
                'current_stage' => 2,
                'progress_percentage' => 5,
            ],
            [
                'full_name' => 'Sunday Aro',
                'matric_number' => '100910031',
                'email' => 'olasunkanmiarowolo@gmail.com',
                'phone' => '08000000000',
                'degree_level' => 'BSc',
                'research_topic' => null,
                'current_stage' => 1,
                'progress_percentage' => 0,
            ],
            [
                'full_name' => 'Adenuga Abdul-Wahab Adedayo',
                'matric_number' => '230910008',
                'email' => 'abdul-wahab.adenuga230910008@st.lasu.edu.ng',
                'phone' => '09138282362',
                'degree_level' => '400 Level',
                'research_topic' => null,
                'current_stage' => 1,
                'progress_percentage' => 0,
            ],
            [
                'full_name' => 'Ajani Esther Titilayo',
                'matric_number' => '230910020',
                'email' => 'esther.ajani230910020@st.lasu.edu.ng',
                'phone' => '07073462043',
                'degree_level' => '400 Level',
                'research_topic' => 'The Role of Television Advertising in promoting Health and Hygiene practices among Residents of Lagos state',
                'current_stage' => 2,
                'progress_percentage' => 10,
            ],
            [
                'full_name' => 'Adekunle Yewande Amanda',
                'matric_number' => '240910471',
                'email' => 'yewande.adekunle240910471@st.lasu.edu.ng',
                'phone' => '07045510572',
                'degree_level' => '400 Level',
                'research_topic' => 'Generative Al and Journalistic Gatekeeping: Assessing News Editors\' Perceptions of Al-Assisted Editorial Decision Making in Selected Lagos Newsrooms',
                'current_stage' => 3,
                'progress_percentage' => 15,
            ],
            [
                'full_name' => 'Olabisi Folashade Victoria',
                'matric_number' => '230910124',
                'email' => 'Folashade.olabisi230910124@st.lasu.edu.ng',
                'phone' => '07026179591',
                'degree_level' => '400 Level',
                'research_topic' => null,
                'current_stage' => 1,
                'progress_percentage' => 0,
            ],
            [
                'full_name' => 'Adetunji Aminat Olaonipekun',
                'matric_number' => '230910362',
                'email' => 'Aminat.adetunji230910362@st.lasu.edu.ng',
                'phone' => '09160344534',
                'degree_level' => '400 Level',
                'research_topic' => null,
                'current_stage' => 1,
                'progress_percentage' => 0,
            ],
            [
                'full_name' => 'Asogba Ayomide Itunuoluwa',
                'matric_number' => '230910036',
                'email' => 'ayomide.asogba230910036@st.lasu.edu.ng',
                'phone' => '09134212378',
                'degree_level' => '400 Level',
                'research_topic' => null,
                'current_stage' => 1,
                'progress_percentage' => 0,
            ],
            [
                'full_name' => 'Otoma Anwulika Happiness',
                'matric_number' => '240910459',
                'email' => 'Otomaanwulika@gmail.com',
                'phone' => '07045743341',
                'degree_level' => '400 Level',
                'research_topic' => null,
                'current_stage' => 1,
                'progress_percentage' => 0,
            ],
            [
                'full_name' => 'Benjamin Jennifer Oluchukwu',
                'matric_number' => '230910047',
                'email' => 'jennifer.benjamin230910047@st.lasu.ng.edu',
                'phone' => '09015320886',
                'degree_level' => '400 Level',
                'research_topic' => null,
                'current_stage' => 1,
                'progress_percentage' => 0,
            ],
            [
                'full_name' => 'FASHANU TITILAYO DEBORAH',
                'matric_number' => '230910064',
                'email' => 'titilayo.fashanu230910064@st.lasu.edu.ng',
                'phone' => '09064885536',
                'degree_level' => '400 Level',
                'research_topic' => 'How Public Relations Helps Organisations Handle Customer Complaints and Build Trust',
                'current_stage' => 2,
                'progress_percentage' => 10,
            ],
            [
                'full_name' => 'Hassan Hafsah Oyinkansola',
                'matric_number' => '230910073',
                'email' => 'hafsah.hassan230910073@st.lasu.edu.ng',
                'phone' => '07061675394',
                'degree_level' => '400 Level',
                'research_topic' => 'Research topic approved',
                'current_stage' => 2,
                'progress_percentage' => 10,
            ],
            [
                'full_name' => 'Ayanniyi Ayoola Abdulmalilk',
                'matric_number' => '230910203',
                'email' => 'abdulmalik.ayanniyi230910203@st.lasu.edu.ng',
                'phone' => '07088013750',
                'degree_level' => '400 Level',
                'research_topic' => null,
                'current_stage' => 1,
                'progress_percentage' => 0,
            ],
        ];

        foreach ($arowoloStudents as $studentData) {
            $user = User::updateOrCreate(
                ['email' => $studentData['email']],
                [
                    'university_id' => 1,
                    'password' => Hash::make('Student@2026'),
                    'name' => $studentData['full_name'],
                    'role' => 'student',
                ]
            );

            Student::updateOrCreate(
                ['user_id' => $user->id],
                [
                    'user_id' => $user->id,
                    'university_id' => 1,
                    'matric_number' => $studentData['matric_number'],
                    'lastname' => explode(' ', $studentData['full_name'])[count(explode(' ', $studentData['full_name'])) - 1],
                    'full_name' => $studentData['full_name'],
                    'email' => $studentData['email'],
                    'phone' => $studentData['phone'],
                    'degree_level' => $studentData['degree_level'],
                    'research_topic' => $studentData['research_topic'] ?? null,
                    'research_topic_approved_date' => $studentData['research_topic'] ? now()->subDays(random_int(15, 60)) : null,
                    'current_stage' => $studentData['current_stage'],
                    'progress_percentage' => $studentData['progress_percentage'],
                    'points_earned' => $studentData['progress_percentage'] * 2,
                    'status' => 'active',
                    'personal_drive_url' => null,
                    'last_meeting_date' => $studentData['progress_percentage'] > 0 ? now()->subDays(random_int(7, 30)) : null,
                ]
            );
        }

        // ============ TEST STUDENT FOR OLAAROWOLO.UK (olaarowolo.uk@gmail.com) ============
        // Supervisor: Dr. Olasunkanmi Arowolo (UK)
        
        $user = User::updateOrCreate(
            ['email' => 'oamediakraft@gmail.com'],
            [
                'university_id' => 1,
                'password' => Hash::make('Student@2026'),
                'name' => 'Oamediakraft Test Student',
                'role' => 'student',
            ]
        );

        Student::updateOrCreate(
            ['user_id' => $user->id],
            [
                'user_id' => $user->id,
                'university_id' => 1,
                'matric_number' => 'LASU/TEST/001',
                'lastname' => 'Test',
                'full_name' => 'Oamediakraft Test Student',
                'email' => 'oamediakraft@gmail.com',
                'phone' => '08000000000',
                'degree_level' => 'MSc',
                'research_topic' => null,
                'current_stage' => 1,
                'progress_percentage' => 0,
                'points_earned' => 0,
                'status' => 'active',
                'personal_drive_url' => null,
                'last_meeting_date' => null,
            ]
        );
    }
}
