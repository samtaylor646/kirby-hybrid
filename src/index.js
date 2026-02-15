/**
 * src/index.js — Vite JS Entry Point
 * Kirby 5.3 / Vite 7 / Tailwind 4 / GSAP
 */

import './index.css';
import { gsap } from 'gsap';
import { ScrollTrigger } from 'gsap/ScrollTrigger';

gsap.registerPlugin(ScrollTrigger);

console.log('Kirby + Vite 7 + Tailwind 4 + GSAP loaded.');

window.addEventListener('load', () => {

  // ── Cursor ───────────────────────────────────────────────────────────────
  const cursor   = document.getElementById('cursor');
  const follower = document.getElementById('cursor-follower');
  if (cursor && follower) {
    document.addEventListener('mousemove', (e) => {
      gsap.to(cursor,   { x: e.clientX,      y: e.clientY,      duration: 0   });
      gsap.to(follower, { x: e.clientX - 22, y: e.clientY - 22, duration: 0.1 });
    });
  }

  // ── Magnetic Buttons ─────────────────────────────────────────────────────
  document.querySelectorAll('.btn-magnetic').forEach(btn => {
    btn.addEventListener('mousemove', (e) => {
      const rect = btn.getBoundingClientRect();
      gsap.to(btn, {
        x: (e.clientX - rect.left - rect.width  / 2) * 0.4,
        y: (e.clientY - rect.top  - rect.height / 2) * 0.4,
        duration: 0.3, ease: 'power2.out',
      });
    });
    btn.addEventListener('mouseleave', () => {
      gsap.to(btn, { x: 0, y: 0, duration: 0.5, ease: 'elastic.out(1, 0.3)' });
    });
  });

  // ── Scroll Reveals ────────────────────────────────────────────────────────
  document.querySelectorAll('.reveal').forEach(el => {
    ScrollTrigger.create({
      trigger: el, start: 'top 88%',
      onEnter: () => el.classList.add('active'), once: true,
    });
  });

  // ── Hero on-load sequence ─────────────────────────────────────────────────
  const heroTitle = document.getElementById('heroTitle');
  if (heroTitle) heroTitle.classList.add('active');

  const ctaReveal = document.getElementById('ctaBtn')?.closest('.reveal');
  if (ctaReveal) setTimeout(() => ctaReveal.classList.add('active'), 500);

  const scrollIndicator = document.getElementById('scrollIndicator');
  if (scrollIndicator) {
    setTimeout(() => {
      scrollIndicator.classList.add('active');
      setTimeout(() => {
        scrollIndicator.style.transition = 'none';
        gsap.to('#scrollIndicator', {
          scrollTrigger: { trigger: '.hero', start: 'top top', end: 'bottom center', scrub: true },
          y: -150, opacity: 0,
        });
      }, 1000);
    }, 1200);
  }

  // ── Solutions — Stacking / Pinned Scroll Animation ───────────────────────
  // .card-copy divs collapse sequentially as the user scrolls.
  // The strategy section slides up underneath the collapsing solutions section.
  const copies          = document.querySelectorAll('.card-copy');
  const cards           = document.querySelectorAll('.service-card');
  const solutionsSection = document.getElementById('solutions');
  const strategySection  = document.getElementById('strategy');

  if (copies.length && cards.length && solutionsSection && strategySection) {
    const tl = gsap.timeline();
    copies.forEach((copy, i) => {
      const card = cards[i];
      tl.to({}, { duration: 1 });
      tl.to(copy, { height: 0, opacity: 0, marginBottom: 0, duration: 1, ease: 'power2.inOut' }, `step-${i}`);
      tl.to(card, { paddingTop: '2rem', paddingBottom: '0rem', duration: 1, ease: 'power2.inOut' }, `step-${i}`);
    });
    tl.to(solutionsSection, { paddingBottom: '2rem', height: '800px', maxHeight: '800px', duration: 1 }, '<');
    tl.to(strategySection,  { marginTop: '-1250px', duration: 1 }, '<');

    ScrollTrigger.create({
      trigger:   '#solutions',
      start:     '150px top',
      end:       '+=2000',
      pin:       true,
      scrub:     1,
      animation: tl,
    });
  }

  // ── Nav hide/show ─────────────────────────────────────────────────────────
  let lastScrollTop = 0;
  const nav = document.getElementById('mainNav');
  if (nav) {
    window.addEventListener('scroll', () => {
      const st = window.pageYOffset || document.documentElement.scrollTop;
      if (st <= 0) {
        nav.classList.remove('nav-hidden', 'nav-scrolled');
      } else if (st > lastScrollTop && st > 50) {
        nav.classList.add('nav-hidden');
      } else if (st < lastScrollTop) {
        nav.classList.remove('nav-hidden');
        nav.classList.add('nav-scrolled');
      }
      lastScrollTop = st <= 0 ? 0 : st;
    }, { passive: true });
  }

});

// ── Strategy cell expand / collapse ──────────────────────────────────────────
const galleryIntervals = new WeakMap();

function startGallery(cell) {
  const gallery = cell.querySelector('[data-gallery]');
  if (!gallery) return;
  const slides = Array.from(gallery.querySelectorAll('.gallery-slide'));
  if (slides.length < 2) return;
  let current = 0;
  const interval = setInterval(() => {
    slides[current].classList.remove('active');
    current = (current + 1) % slides.length;
    slides[current].classList.add('active');
  }, 3000);
  galleryIntervals.set(gallery, interval);
}

function stopGallery(cell) {
  const gallery = cell.querySelector('[data-gallery]');
  if (!gallery) return;
  const interval = galleryIntervals.get(gallery);
  if (interval) {
    clearInterval(interval);
    galleryIntervals.delete(gallery);
    gallery.querySelectorAll('.gallery-slide').forEach((s, i) => s.classList.toggle('active', i === 0));
  }
}

window.expandStrategyCell = function (cell) {
  if (cell.classList.contains('active')) return;
  document.querySelectorAll('.strategy-cell').forEach(c => {
    if (c !== cell) c.style.display = 'none';
  });
  cell.classList.add('active');
  startGallery(cell);
  setTimeout(() => ScrollTrigger.refresh(), 150);
};

window.closeStrategyCell = function (event, btn) {
  event.stopPropagation();
  const cell = btn.closest('.strategy-cell');
  stopGallery(cell);
  cell.classList.remove('active');
  setTimeout(() => {
    document.querySelectorAll('.strategy-cell').forEach(c => {
      c.style.display = 'flex';
    });
    ScrollTrigger.refresh();
  }, 500);
};

// ── AI Consultant ─────────────────────────────────────────────────────────────
async function callGemini(prompt) {
  const key = window.GEMINI_KEY || '';
  const systemPrompt = 'You are the Westport Partners AI Architect. Provide professional federal modernization roadmaps. Use bullet points. Keep it punchy and high-level.';
  let retries = 0;
  while (retries < 5) {
    try {
      const res  = await fetch(`https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key=${key}`, {
        method: 'POST', headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ contents: [{ parts: [{ text: prompt }] }], systemInstruction: { parts: [{ text: systemPrompt }] } }),
      });
      const data = await res.json();
      return data.candidates?.[0]?.content?.parts?.[0]?.text;
    } catch (err) {
      retries++;
      await new Promise(r => setTimeout(r, Math.pow(2, retries) * 500));
    }
  }
}

window.generateAIAudit = async function () {
  const input       = document.getElementById('userInput');
  const btn         = document.getElementById('aiSubmitBtn');
  const resDiv      = document.getElementById('aiResponse');
  const resText     = document.getElementById('responseText');
  if (!input?.value.trim()) return;
  const label = btn.dataset.label || btn.innerText;
  btn.disabled = true; btn.innerText = 'Architecting... ✨';
  resDiv.style.display = 'block';
  resText.innerHTML = '<span class="loading-dots">Analyzing technical debt</span>';
  try {
    const result = await callGemini(input.value);
    resText.innerHTML = `<strong>Roadmap Preview:</strong><br><br>${result.replace(/\n/g, '<br>')}`;
  } catch {
    resText.innerHTML = 'Error reaching architect. Check connectivity.';
  } finally {
    btn.disabled = false; btn.innerText = label;
  }
};

// ── ECharts lazy load ─────────────────────────────────────────────────────────
(async () => {
  const targets = document.querySelectorAll('[data-echart]');
  if (targets.length > 0) {
    const echarts = await import('echarts');
    targets.forEach(el => echarts.init(el).setOption(JSON.parse(el.dataset.options)));
  }
})();
