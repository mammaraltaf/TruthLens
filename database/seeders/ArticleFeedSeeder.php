<?php

namespace Database\Seeders;

use App\Enums\ArticleStatus;
use App\Enums\ArticleSubmissionType;
use App\Enums\VoteType;
use App\Models\Article;
use App\Models\ArticleVote;
use App\Models\Badge;
use App\Models\FactCheckResult;
use App\Models\Source;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ArticleFeedSeeder extends Seeder
{
    public function run(): void
    {
        $demo = User::query()->where('email', 'user@truthlens.local')->first();
        $admin = User::query()->where('email', 'admin@truthlens.local')->first();

        if (! $demo || ! $admin) {
            $this->command?->warn('Skipping article seed: demo users not found. Run DatabaseSeeder first.');

            return;
        }

        ArticleVote::query()->delete();
        FactCheckResult::query()->delete();
        Article::query()->delete();
        Source::query()->update(['article_count' => 0]);

        $badges = Badge::query()->pluck('id', 'slug');

        $rows = [
            [
                'user_id' => $demo->id,
                'title' => 'Study: routine hydration linked to better focus in warm climates',
                'category' => 'Health',
                'url' => 'https://healthbrief.example/news/hydration-focus-study',
                'score' => 82.00,
                'slug' => 'trusted',
                'days_ago' => 1,
                'content' => <<<'TXT'
Public health researchers published a summary of a small clinical observational study suggesting that adults who maintained fluid intake within recommended ranges during heat warnings reported fewer self-reported concentration lapses. The paper explicitly notes correlation, not proven causation, and calls for larger trials. Major outlets condensed the findings into shorter headlines; the full preprint stresses limitations and funding disclosures.
TXT,
                'fact_check' => [
                    'claims' => [[
                        'claimReview' => [[
                            'title' => 'Context matters for hydration headlines',
                            'url' => 'https://example-factcheck.org/hydration-study',
                            'publisher' => ['name' => 'Sample Fact Lab'],
                            'textualRating' => 'Mostly true',
                        ]],
                    ]],
                ],
            ],
            [
                'user_id' => $admin->id,
                'title' => 'City council approves revised transit budget after public hearings',
                'category' => 'Politics',
                'url' => 'https://metrowire.example/local/transit-budget-2026',
                'score' => 76.50,
                'slug' => 'trusted',
                'days_ago' => 2,
                'content' => <<<'TXT'
After three weeks of hearings, the city council voted 7–2 to approve a revised capital plan for bus lane expansion. The mayor's office published the final line-item spreadsheet on the municipal open data portal. Opposition members cited incomplete rider surveys; the majority emphasized on-time performance metrics from the transit agency's Q3 report.
TXT,
                'fact_check' => null,
            ],
            [
                'user_id' => $demo->id,
                'title' => 'Viral post claims moon landing footage was “newly leaked” — metadata tells another story',
                'category' => 'Science',
                'url' => 'https://orbitblog.example/moon-footage-rumor',
                'score' => 48.00,
                'slug' => 'suspicious',
                'days_ago' => 3,
                'content' => <<<'TXT'
A widely shared clip on social media alleges never-before-seen Apollo footage. Archivists matched the clip to a digitized reel uploaded by a national agency in 2018. The viral post added dramatic music and cropped timestamps, which makes the material appear novel. Independent researchers recommend checking primary archival sources before resharing.
TXT,
                'fact_check' => [
                    'claims' => [[
                        'claimReview' => [[
                            'title' => 'Old footage, new packaging',
                            'url' => 'https://example-factcheck.org/apollo-clip',
                            'publisher' => ['name' => 'Sample Fact Lab'],
                            'textualRating' => 'Misleading',
                        ]],
                    ]],
                ],
            ],
            [
                'user_id' => $demo->id,
                'title' => 'Celebrity “miracle cure” quote spreads without original interview transcript',
                'category' => 'Health',
                'url' => 'https://gossippulse.example/celebrity-cure-quote',
                'score' => 22.00,
                'slug' => 'fake',
                'days_ago' => 4,
                'content' => <<<'TXT'
Screenshots attribute a lengthy quote about an unapproved supplement to a television actor. The show's production company stated no such segment aired; the quote first appeared on an anonymous forum. Pharmacists warn that the product named in the thread is not evaluated for the diseases mentioned in the caption.
TXT,
                'fact_check' => [
                    'claims' => [[
                        'claimReview' => [[
                            'title' => 'Fabricated quote',
                            'url' => 'https://example-factcheck.org/celebrity-quote',
                            'publisher' => ['name' => 'Sample Fact Lab'],
                            'textualRating' => 'False',
                        ]],
                    ]],
                ],
            ],
            [
                'user_id' => $admin->id,
                'title' => 'Local team wins regional match in overtime',
                'category' => 'Sports',
                'url' => 'https://sportsdesk.example/regional-final-recap',
                'score' => 71.25,
                'slug' => 'trusted',
                'days_ago' => 5,
                'content' => <<<'TXT'
The Riverside Athletics secured a 3–2 overtime win against Harbor City in the regional semifinal. The league's official scoresheet lists goals by period and referee assignments. Broadcast partners replayed the winning shot from two camera angles; fan footage from the upper deck matches the game clock.
TXT,
                'fact_check' => null,
            ],
            [
                'user_id' => $demo->id,
                'title' => 'Economists debate inflation outlook after latest quarterly report',
                'category' => 'Economy',
                'url' => null,
                'submission_type' => ArticleSubmissionType::Text,
                'score' => 58.00,
                'slug' => 'suspicious',
                'days_ago' => 6,
                'content' => <<<'TXT'
Analysts disagree on whether the latest quarterly inflation print signals a sustained downtrend or a temporary dip driven by energy prices. The statistical agency's technical note highlights seasonal adjustment choices. Several opinion columns extrapolate bold policy conclusions beyond what the official release states; readers should separate data tables from columnist interpretation.
TXT,
                'fact_check' => null,
            ],
            [
                'user_id' => $admin->id,
                'title' => 'Undocumented claim about Mars sample return timeline circulates on forums',
                'category' => 'Science',
                'url' => 'https://speculatenow.example/mars-sample-claim',
                'score' => null,
                'slug' => 'unverified',
                'days_ago' => 7,
                'content' => <<<'TXT'
Forum users speculate about an accelerated Mars sample mission schedule based on an unsourced timeline graphic. Space agencies have published roadmaps with different milestone years; no public contract amendment matches the graphic's dates. This entry is useful as a teaching example for checking primary agency communications.
TXT,
                'fact_check' => null,
            ],
            [
                'user_id' => $demo->id,
                'title' => 'Satire article about talking pets mistaken for breaking news',
                'category' => 'Culture',
                'url' => 'https://weeklysilly.example/pet-translator-satire',
                'score' => 35.00,
                'slug' => 'suspicious',
                'days_ago' => 8,
                'content' => <<<'TXT'
A humor site's headline about a "court-certified pet translator" was cropped to remove the site's satire label and reshared as fact. The original page footer marks all stories as fictional. Animal behavior researchers note that audio classifiers exist for species sounds but not courtroom-ready translation in the manner described.
TXT,
                'fact_check' => [
                    'claims' => [[
                        'claimReview' => [[
                            'title' => 'Satire stripped of context',
                            'url' => 'https://example-factcheck.org/pet-satire',
                            'publisher' => ['name' => 'Sample Fact Lab'],
                            'textualRating' => 'False',
                        ]],
                    ]],
                ],
            ],
        ];

        foreach ($rows as $def) {
            $normalized = trim(preg_replace('/\s+/u', ' ', $def['content']) ?? '');
            $hash = hash('sha256', $normalized);

            $sourceId = null;
            if (! empty($def['url']) && is_string($def['url'])) {
                $host = parse_url($def['url'], PHP_URL_HOST);
                if (is_string($host)) {
                    $host = Str::lower(preg_replace('/^www\./', '', $host) ?? $host);
                    $source = Source::query()->firstOrCreate(
                        ['domain' => $host],
                        ['trust_score' => 50, 'is_banned' => false, 'article_count' => 0]
                    );
                    $sourceId = $source->id;
                }
            }

            $submissionType = $def['submission_type'] ?? ArticleSubmissionType::Url;

            $processed = now()->subDays($def['days_ago'])->subHours(random_int(0, 12));
            $stamped = (clone $processed)->subMinutes(random_int(5, 90));

            $article = Article::query()->create([
                'user_id' => $def['user_id'],
                'source_id' => $sourceId,
                'badge_id' => $badges[$def['slug']] ?? null,
                'submission_type' => $submissionType,
                'url' => $def['url'] ?? null,
                'title' => $def['title'],
                'content' => Str::limit($normalized, 50_000, ''),
                'content_hash' => $hash,
                'category' => $def['category'],
                'credibility_score' => $def['score'],
                'status' => ArticleStatus::Completed,
                'processed_at' => $processed,
            ]);

            $article->forceFill([
                'created_at' => $stamped,
                'updated_at' => $stamped,
            ])->saveQuietly();

            if (! empty($def['fact_check'])) {
                $score = $def['score'];
                $verdict = match (true) {
                    $score === null => null,
                    $score >= 70 => 'mostly_supported',
                    $score >= 40 => 'mixed',
                    default => 'mostly_disputed',
                };
                FactCheckResult::query()->create([
                    'article_id' => $article->id,
                    'api_response' => $def['fact_check'],
                    'computed_score' => $score,
                    'verdict' => $verdict,
                ]);
            }

            if ($sourceId) {
                Source::query()->whereKey($sourceId)->increment('article_count');
            }
        }

        $articles = Article::query()->where('status', ArticleStatus::Completed)->orderBy('id')->get();

        if ($articles->count() >= 3) {
            $votes = [
                [$articles[0]->id, $demo->id, VoteType::Real],
                [$articles[0]->id, $admin->id, VoteType::Real],
                [$articles[1]->id, $demo->id, VoteType::Real],
                [$articles[1]->id, $admin->id, VoteType::Fake],
                [$articles[2]->id, $demo->id, VoteType::Fake],
                [$articles[3]->id, $admin->id, VoteType::Fake],
                [$articles[4]->id, $demo->id, VoteType::Real],
            ];

            foreach ($votes as [$articleId, $userId, $type]) {
                ArticleVote::query()->create([
                    'article_id' => $articleId,
                    'user_id' => $userId,
                    'vote_type' => $type,
                ]);
            }
        }
    }
}
