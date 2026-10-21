<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MyBlog weekly summary</title>
</head>
<body style="margin:0;padding:24px;background:#f1f5f9;font-family:Arial,Helvetica,sans-serif;color:#0f172a;">
    <div style="max-width:560px;margin:0 auto;background:#ffffff;border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;">

        <div style="background:#4f46e5;padding:24px;color:#ffffff;">
            <h1 style="margin:0;font-size:20px;">MyBlog weekly summary</h1>
            <p style="margin:6px 0 0;font-size:13px;opacity:.85;">
                {{ $data['from'] }} &ndash; {{ $data['to'] }}
            </p>
        </div>

        <div style="padding:24px;">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="margin-bottom:20px;">
                <tr>
                    <td width="50%" style="padding:0 6px 12px 0;">
                        <div style="background:#eef2ff;border-radius:10px;padding:14px;">
                            <div style="font-size:12px;color:#64748b;">New posts</div>
                            <div style="font-size:24px;font-weight:bold;">{{ $data['new_posts'] }}</div>
                        </div>
                    </td>
                    <td width="50%" style="padding:0 0 12px 6px;">
                        <div style="background:#ecfdf5;border-radius:10px;padding:14px;">
                            <div style="font-size:12px;color:#64748b;">New users</div>
                            <div style="font-size:24px;font-weight:bold;">{{ $data['new_users'] }}</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td width="50%" style="padding:0 6px 0 0;">
                        <div style="background:#fffbeb;border-radius:10px;padding:14px;">
                            <div style="font-size:12px;color:#64748b;">Pending approval</div>
                            <div style="font-size:24px;font-weight:bold;">{{ $data['pending'] }}</div>
                        </div>
                    </td>
                    <td width="50%" style="padding:0 0 0 6px;">
                        <div style="background:#faf5ff;border-radius:10px;padding:14px;">
                            <div style="font-size:12px;color:#64748b;">Total views (all time)</div>
                            <div style="font-size:24px;font-weight:bold;">{{ number_format($data['total_views']) }}</div>
                        </div>
                    </td>
                </tr>
            </table>

            <p style="font-size:13px;color:#64748b;margin:0 0 16px;">
                {{ $data['activity_count'] }} actions were recorded in the activity log this week.
            </p>

            <h2 style="font-size:15px;margin:0 0 10px;">Most viewed posts</h2>
            @forelse ($data['top_posts'] as $post)
                <div style="padding:10px 0;border-top:1px solid #e2e8f0;font-size:14px;">
                    <a href="{{ route('posts.show', $post) }}" style="color:#4f46e5;text-decoration:none;font-weight:bold;">
                        {{ $post->title }}
                    </a>
                    <span style="float:right;color:#64748b;">{{ number_format($post->views) }} views</span>
                </div>
            @empty
                <p style="font-size:14px;color:#64748b;">No published posts yet.</p>
            @endforelse

            @if ($data['pending'] > 0)
                <p style="margin:24px 0 0;">
                    <a href="{{ route('posts.pending') }}"
                       style="display:inline-block;background:#4f46e5;color:#ffffff;text-decoration:none;padding:10px 18px;border-radius:8px;font-size:14px;font-weight:bold;">
                        Review pending posts
                    </a>
                </p>
            @endif
        </div>

        <div style="padding:16px 24px;background:#f8fafc;font-size:12px;color:#94a3b8;text-align:center;">
            Sent automatically by MyBlog every Monday.
        </div>
    </div>
</body>
</html>