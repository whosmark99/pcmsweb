<?php
/**
 * Renders the PCMS brand logo.
 *
 * @param string $variant  'gradient' (default, user) | 'admin' | 'mono'
 * @param int    $size     Pixel size of the square mark (default 40)
 * @param bool   $withText Whether to render the "PCM System" wordmark
 */
function render_logo(string $variant = 'gradient', int $size = 40, bool $withText = false): void
{
    $gradients = [
        'gradient' => ['#6366f1', '#8b5cf6'],
        'admin'    => ['#dc2626', '#f97316'],
        'mono'     => ['#475569', '#1e293b'],
    ];
    [$c1, $c2] = $gradients[$variant] ?? $gradients['gradient'];
    $gid = 'pcmsGrad_' . substr(md5($variant), 0, 6);
    ?>
    <div class="brand-mark" style="--logo-size:<?= (int)$size ?>px;">
        <svg viewBox="0 0 64 64" width="<?= (int)$size ?>" height="<?= (int)$size ?>" fill="none" aria-hidden="true">
            <defs>
                <linearGradient id="<?= $gid ?>" x1="0" y1="0" x2="1" y2="1">
                    <stop offset="0%" stop-color="<?= htmlspecialchars($c1) ?>"/>
                    <stop offset="100%" stop-color="<?= htmlspecialchars($c2) ?>"/>
                </linearGradient>
            </defs>
            <rect x="2" y="2" width="60" height="60" rx="16" fill="url(#<?= $gid ?>)"/>
            <path d="M20 44V22c0-1.1.9-2 2-2h9c4.4 0 8 3.6 8 8s-3.6 8-8 8h-5"
                  stroke="#ffffff" stroke-width="3.6" stroke-linecap="round" stroke-linejoin="round"/>
            <circle cx="42" cy="42" r="3.2" fill="#ffffff"/>
        </svg>
        <?php if ($withText): ?>
            <span class="brand-wordmark">PCM<span>System</span></span>
        <?php endif; ?>
    </div>
    <?php
}