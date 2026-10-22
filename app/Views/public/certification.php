<?php
use app\Models\Certification;

$code = $state['code'];
$isValid = in_array($code, ['VALID', 'EXPIRING'], true);
$isUpcoming = $code === 'UPCOMING';
$headline = $isValid ? 'VIGENTE' : ($isUpcoming ? 'AÚN NO VIGENTE' : 'NO VIGENTE');
if ($isValid) {
    $caption = 'Certificación válida emitida por el Tribunal de Justicia Administrativa del Estado de Chiapas.';
} elseif ($isUpcoming) {
    $caption = 'Certificación válida; su vigencia inicia el ' . Certification::formatDate($record['valid_from']) . '.';
} elseif ($code === 'REVOKED') {
    $caption = 'Esta certificación fue revocada por el órgano emisor.';
} else {
    $caption = 'El periodo de vigencia de esta certificación ha concluido.';
}

$theme = $isValid
    ? ['band' => 'from-emerald-600 via-emerald-500 to-teal-500', 'icon' => 'verified', 'ring' => 'ring-emerald-200', 'text' => 'text-emerald-700', 'bar' => 'bg-emerald-500']
    : ($isUpcoming
        ? ['band' => 'from-amber-500 via-amber-500 to-yellow-500', 'icon' => 'schedule', 'ring' => 'ring-amber-200', 'text' => 'text-amber-700', 'bar' => 'bg-amber-500']
        : ['band' => 'from-red-700 via-red-600 to-rose-600', 'icon' => 'cancel', 'ring' => 'ring-red-200', 'text' => 'text-red-700', 'bar' => 'bg-red-500']);

// Avance del periodo de vigencia (0-100%).
$tz = new \DateTimeZone(Certification::TIMEZONE);
$from = new \DateTimeImmutable($record['valid_from'], $tz);
$until = new \DateTimeImmutable($record['valid_until'], $tz);
$today = Certification::today();
$totalDays = max(1, (int)$from->diff($until)->format('%a') + 1);
$elapsed = (int)$from->diff($today)->format('%r%a') + 1;
$progress = max(0, min(100, round($elapsed / $totalDays * 100)));
$daysLeft = $state['days_left'];
$now = new \DateTimeImmutable('now', $tz);
?>
<style>
    .cert-bg {
        background-color: #F3F6FB;
        background-image:
            radial-gradient(circle at 20% 10%, rgba(196, 175, 107, .18) 0, transparent 40%),
            radial-gradient(circle at 85% 90%, rgba(27, 69, 111, .14) 0, transparent 45%),
            repeating-radial-gradient(circle at 50% -20%, transparent 0 22px, rgba(196, 175, 107, .08) 22px 23px);
    }
    .cert-paper {
        background-image:
            linear-gradient(rgba(255,255,255,.96), rgba(255,255,255,.96)),
            repeating-linear-gradient(45deg, rgba(27,69,111,.05) 0 2px, transparent 2px 14px),
            repeating-linear-gradient(-45deg, rgba(196,175,107,.07) 0 2px, transparent 2px 14px);
    }
    .cert-watermark {
        position: absolute; inset: 0; display: flex; align-items: center; justify-content: center;
        pointer-events: none; overflow: hidden;
    }
    .cert-watermark span {
        transform: rotate(-24deg); font-weight: 900; letter-spacing: .15em; white-space: nowrap;
        font-size: clamp(2.75rem, 12vw, 6.5rem); color: rgba(220, 38, 38, .09); border: .5rem solid rgba(220, 38, 38, .09);
        padding: .25rem 1.5rem; border-radius: 1rem;
    }
    @keyframes certPop { 0% { transform: scale(.4); opacity: 0 } 70% { transform: scale(1.12) } 100% { transform: scale(1); opacity: 1 } }
    @keyframes certRise { from { transform: translateY(18px); opacity: 0 } to { transform: none; opacity: 1 } }
    @keyframes certPulse { 0% { box-shadow: 0 0 0 0 rgba(255,255,255,.55) } 100% { box-shadow: 0 0 0 22px rgba(255,255,255,0) } }
    .cert-pop { animation: certPop .6s cubic-bezier(.2,.9,.3,1.3) both .15s; }
    .cert-rise { animation: certRise .6s ease-out both; }
    .cert-pulse { animation: certPulse 1.8s ease-out infinite 1s; }
    .cert-progress { transition: width 1.2s ease-out .4s; }
    @media (prefers-reduced-motion: reduce) { .cert-pop, .cert-rise, .cert-pulse { animation: none; } .cert-progress { transition: none; } }
</style>

<section class="cert-bg px-4 py-8 md:py-14 min-h-[calc(100vh-5rem)]">
    <div class="max-w-3xl mx-auto cert-rise">
        <article class="rounded-3xl overflow-hidden shadow-2xl shadow-slate-900/10 ring-1 ring-slate-900/5 bg-white">
            <!-- Banda de estado -->
            <div class="relative bg-gradient-to-r <?php echo $theme['band']; ?> text-white px-5 py-6 sm:px-8 sm:py-8 overflow-hidden">
                <span class="material-symbols-outlined absolute -right-6 -top-8 text-[160px] sm:text-[200px] opacity-10 select-none" aria-hidden="true"><?php echo $theme['icon']; ?></span>
                <div class="relative flex items-center gap-4 sm:gap-6">
                    <div class="cert-pop shrink-0 w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-white/20 backdrop-blur flex items-center justify-center ring-4 ring-white/30 <?php echo $isValid ? 'cert-pulse' : ''; ?>">
                        <span class="material-symbols-outlined text-[40px] sm:text-[52px]" style="font-variation-settings:'FILL' 1"><?php echo $theme['icon']; ?></span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-[11px] sm:text-xs font-semibold uppercase tracking-[.2em] text-white/80">Resultado de la verificación</p>
                        <h1 class="text-3xl sm:text-5xl font-extrabold tracking-tight leading-none mt-1"><?php echo $headline; ?></h1>
                        <p class="text-sm sm:text-base text-white/90 mt-2 leading-snug"><?php echo htmlspecialchars($caption); ?></p>
                    </div>
                </div>
            </div>

            <!-- Documento -->
            <div class="relative cert-paper px-5 py-8 sm:px-10 sm:py-10">
                <?php if (!$isValid && !$isUpcoming): ?>
                    <div class="cert-watermark" aria-hidden="true"><span><?php echo $code === 'REVOKED' ? 'REVOCADA' : 'NO VIGENTE'; ?></span></div>
                <?php endif; ?>

                <header class="relative flex flex-col items-center text-center">
                    <div class="flex items-center justify-center gap-5 sm:gap-8">
                        <img src="<?php echo base_url('assets/img/logo-tja.png'); ?>" alt="Tribunal de Justicia Administrativa del Estado de Chiapas" class="h-16 sm:h-20 w-auto">
                        <span class="h-12 w-px bg-slate-200"></span>
                        <img src="<?php echo base_url('assets/img/justicia-humanismo.png'); ?>" alt="Justicia con Humanismo" class="h-12 sm:h-16 w-auto">
                    </div>
                    <p class="mt-5 text-[11px] sm:text-xs font-bold uppercase tracking-[.25em] text-slate-500">Tribunal de Justicia Administrativa del Estado de Chiapas</p>
                    <p class="mt-1 text-xs sm:text-sm text-slate-500 max-w-xl leading-snug"><?php echo htmlspecialchars(Certification::AREA_NAME); ?></p>
                    <div class="flex items-center gap-3 w-full max-w-sm mt-5">
                        <span class="flex-1 h-px bg-gradient-to-r from-transparent to-[#C4AF6B]"></span>
                        <span class="material-symbols-outlined text-[#C4AF6B] text-lg">workspace_premium</span>
                        <span class="flex-1 h-px bg-gradient-to-l from-transparent to-[#C4AF6B]"></span>
                    </div>
                    <h2 class="mt-3 text-lg sm:text-xl font-extrabold tracking-[.3em] text-[#111426]">CERTIFICACIÓN</h2>
                </header>

                <dl class="relative mt-8 space-y-6">
                    <div class="text-center">
                        <dt class="text-[11px] font-bold uppercase tracking-[.2em] text-slate-400">Nombre de la persona certificada</dt>
                        <dd class="mt-2 text-2xl sm:text-3xl font-extrabold text-[#1b456f] leading-tight break-words"><?php echo htmlspecialchars($record['full_name']); ?></dd>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-[auto,1fr] gap-4">
                        <div class="rounded-2xl border border-[#C4AF6B]/40 bg-[#C4AF6B]/10 px-6 py-4 text-center sm:min-w-[11rem]">
                            <dt class="text-[11px] font-bold uppercase tracking-[.2em] text-[#8a7638]">Número de certificación</dt>
                            <dd class="mt-1 text-4xl font-black text-[#111426] tabular-nums"><?php echo htmlspecialchars($record['cert_number']); ?></dd>
                        </div>
                        <div class="rounded-2xl border border-slate-200 bg-white/70 px-6 py-4 flex flex-col justify-center text-center sm:text-left">
                            <dt class="text-[11px] font-bold uppercase tracking-[.2em] text-slate-400">Materia</dt>
                            <dd class="mt-1 text-lg sm:text-xl font-bold text-slate-800 leading-snug"><?php echo htmlspecialchars($record['subject']); ?></dd>
                        </div>
                    </div>

                    <div class="rounded-2xl border border-slate-200 bg-white/70 px-5 py-5 sm:px-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <dt class="text-[11px] font-bold uppercase tracking-[.2em] text-slate-400">Vigencia</dt>
                                <dd class="mt-1 text-base sm:text-lg font-semibold text-slate-800">
                                    Del <?php echo Certification::formatDate($record['valid_from']); ?>
                                    <span class="whitespace-nowrap">al <?php echo Certification::formatDate($record['valid_until']); ?></span>
                                </dd>
                            </div>
                            <span class="self-start sm:self-center inline-flex items-center gap-1.5 rounded-full px-4 py-1.5 text-sm font-extrabold ring-2 <?php echo $theme['ring'] . ' ' . $theme['text']; ?> bg-white">
                                <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1"><?php echo $theme['icon']; ?></span>
                                <?php echo $headline; ?>
                            </span>
                        </div>
                        <div class="mt-4 h-2.5 rounded-full bg-slate-100 overflow-hidden" role="progressbar" aria-valuenow="<?php echo $progress; ?>" aria-valuemin="0" aria-valuemax="100" aria-label="Avance del periodo de vigencia">
                            <div class="cert-progress h-full rounded-full <?php echo $theme['bar']; ?>" style="width:0" data-width="<?php echo $code === 'REVOKED' ? 100 : $progress; ?>%"></div>
                        </div>
                        <p class="mt-2 text-xs sm:text-sm font-medium <?php echo $theme['text']; ?>">
                            <?php if ($code === 'REVOKED'): ?>
                                Certificación revocada.
                            <?php elseif ($isUpcoming): ?>
                                Inicia en <?php echo -$elapsed + 1; ?> día<?php echo (-$elapsed + 1) === 1 ? '' : 's'; ?>.
                            <?php elseif ($isValid): ?>
                                <?php echo $daysLeft === 0 ? 'Último día de vigencia.' : 'Restan ' . $daysLeft . ' día' . ($daysLeft === 1 ? '' : 's') . ' de vigencia.'; ?>
                            <?php else: ?>
                                Venció hace <?php echo -$daysLeft; ?> día<?php echo $daysLeft === -1 ? '' : 's'; ?>.
                            <?php endif; ?>
                        </p>
                    </div>
                </dl>

                <footer class="relative mt-8 pt-5 border-t border-dashed border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-2 text-[11px] sm:text-xs text-slate-400 text-center sm:text-left">
                    <span class="inline-flex items-center gap-1.5"><span class="material-symbols-outlined text-sm">shield_lock</span> Verificación en línea del TJAECH</span>
                    <span>Consulta: <?php echo Certification::formatDate($now->format('Y-m-d')) . ', ' . $now->format('H:i'); ?> h</span>
                </footer>
            </div>
        </article>

        <p class="mt-6 text-center text-xs sm:text-sm text-slate-500 leading-relaxed px-2">
            La información mostrada corresponde a los registros oficiales del Tribunal de Justicia Administrativa del Estado de Chiapas
            y debe coincidir con los datos consignados en el certificado impreso.
        </p>
    </div>
</section>
<script>
    setTimeout(() => document.querySelectorAll('.cert-progress').forEach(el => el.style.width = el.dataset.width), 60);
</script>
