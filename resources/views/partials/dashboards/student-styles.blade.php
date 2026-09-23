<style>
  /* ===== Entrance animation for tab content and cards ===== */
  .fade-in { animation: fadeIn 0.25s ease-out forwards; }
  @keyframes fadeIn {
    from { opacity: 0; transform: translateY(6px); }
    to   { opacity: 1; transform: translateY(0); }
  }

  /* ===== Skeleton shimmer for loading placeholders ===== */
  .skeleton {
    background: linear-gradient(90deg, rgba(148,163,184,0.15) 25%, rgba(148,163,184,0.30) 37%, rgba(148,163,184,0.15) 63%);
    background-size: 400% 100%;
    animation: shimmer 1.4s ease infinite;
    border-radius: 0.75rem;
  }
  @keyframes shimmer {
    0%   { background-position: 100% 50%; }
    100% { background-position: 0 50%; }
  }

  /* ===== Full-screen loading overlay ===== */
  #loading-overlay {
    transition: opacity 0.3s ease;
  }
  .loading-ring {
    width: 44px; height: 44px;
    border: 3px solid rgba(3,83,136,0.15);
    border-top-color: #035388;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
  }
  @keyframes spin { to { transform: rotate(360deg); } }

  /* Roadmap connector pulse on the active stage */
  @keyframes stagePulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(245,158,11,0.4); }
    50%      { box-shadow: 0 0 0 6px rgba(245,158,11,0); }
  }
  .stage-active { animation: stagePulse 2s ease-in-out infinite; }
</style>
