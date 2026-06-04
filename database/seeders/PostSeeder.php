<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Models\Comment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class PostSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $author = User::where('role', 'author')->first() ?? $admin;
        $users = User::all();
        $categories = Category::all();

        if ($categories->isEmpty() || $users->isEmpty()) {
            $this->command->error("Seed users and categories first!");
            return;
        }

        // 1. Create the Featured Post (Global/Anime Niche)
        $featured = Post::updateOrCreate(
            ['slug' => 'the-future-of-animation-why-solo-leveling-changed-everything'],
            [
                'title'       => 'The Future of Animation: Why Solo Leveling Changed Everything',
                'content'     => 'The global animation landscape is undergoing a massive shift as webtoon adaptations take center stage, disrupting traditional production pipelines and setting new milestones for viewer engagement.',
                'user_id'     => $admin->id,
                'category_id' => $categories->firstWhere('slug', 'movies-anime')->id ?? $categories->random()->id,
                'is_featured' => true,
            ]
        );
        $this->addInteractions($featured, $users);

        // 2. High-Fidelity Articles (Including Nigerian Security & Global Politics)
        $articles = [
            [
                'title'       => 'Security Architecture Overhaul: New Strategies Aimed at Curbing Regional Insecurity in Nigeria',
                'content'     => 'In response to persistent regional security challenges, defensive frameworks across Nigeria are seeing direct tactical updates. Analysts suggest that community-centered intelligence gathering coupled with technology integration will play a critical role in stabilizing high-risk zones.',
                'category'    => 'security-politics',
                'user_id'     => $author->id,
            ],
            [
                'title'       => 'Socio-Economic Impacts of Modern Border Monitoring along the West African Corridor',
                'content'     => 'As geopolitical dynamics shift, economic corridors within West Africa are adapting to tighter security protocols. This deep dive examines how local trade infrastructure reacts to enhanced border surveillance and stabilization initiatives.',
                'category'    => 'security-politics',
                'user_id'     => $author->id,
            ],
            [
                'title'       => 'Global Tech Hubs Shift: How Decentralized Content Networks are Reshaping Journalism',
                'content'     => 'Traditional media monopolies are facing structural decline as decentralized architectures empower independent news networks to report real-time global events without corporate editorial constraints.',
                'category'    => 'global-news',
                'user_id'     => $admin->id,
            ],
            [
                'title'       => 'The Evolution of Narrative Depth in Modern Seinen Manga Ecosystems',
                'content'     => 'Exploring how complex political and psychological themes in contemporary print series are capturing a mature, globally connected demographic of readers looking for nuanced storytelling.',
                'category'    => 'manga',
                'user_id'     => $author->id,
            ],
            [
                'title'       => 'Streaming Platforms vs Box Office: The Multi-Million Dollar Distribution Tug of War',
                'content'     => 'The direct-to-consumer digital distribution pipeline continues to challenge traditional cinematic releases, altering production budgets and licensing agreements globally.',
                'category'    => 'reviews',
                'user_id'     => $admin->id,
            ]
        ];

        // 3. Process the Articles Safely
        foreach ($articles as $article) {
            $slug = Str::slug($article['title']);
            $categoryId = $categories->firstWhere('slug', $article['category'])->id ?? $categories->random()->id;

            $post = Post::updateOrCreate(
                ['slug' => $slug],
                [
                    'title'       => $article['title'],
                    'content'     => $article['content'],
                    'user_id'     => $article['user_id'],
                    'category_id' => $categoryId,
                    'is_featured' => false,
                ]
            );

            $this->addInteractions($post, $users);
        }
    }

    /**
     * Helper function to add production-safe comments and likes to a post
     */
    private function addInteractions($post, $users): void
    {
        // Sample static comments to completely bypass Faker requirements
        $sampleComments = [
            'This is a crucial breakdown of the current situation.',
            'Incredibly well-researched perspective on this topic.',
            'Completely agree with this structural analysis.',
            'An essential read for understanding these macro developments.',
            'Looking forward to the follow-up investigation on this matter.',
        ];

        // Add 2-4 static comments per post to prevent factory errors
        $commentCount = rand(2, 4);
        for ($i = 0; $i < $commentCount; $i++) {
            Comment::updateOrCreate(
                [
                    'post_id' => $post->id,
                    'user_id' => $users->random()->id,
                    'body'    => $sampleComments[$i]
                ]
            );
        }

        // Add user hypes/likes cleanly using existing relationships
        $likers = $users->random(min(rand(2, 5), $users->count()));
        foreach ($likers as $user) {
            // Checks if relation method likes() exists before creating to prevent crashes
            if (method_exists($post, 'likes')) {
                $post->likes()->updateOrCreate(['user_id' => $user->id]);
            }
        }
    }
}