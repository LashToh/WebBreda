document.addEventListener("DOMContentLoaded", function () {
    const WE = window.TEMPLATE;
    const sections = document.querySelectorAll(".panel");
    const nav = document.querySelector(".section-nav");
    const footerEl = document.querySelector("footer");

    // Helper: show footer only on #class-showcase
    function updateFooterVisibility(activeSection) {
        if (!footerEl) return;
        if (activeSection && activeSection.id === "rankings") {
            footerEl.classList.add("footer-visible");
        } else {
            footerEl.classList.remove("footer-visible");
        }
    }

    // ── Preloader Removal ──
    function removePreloader() {
        const preloader = document.getElementById("preloader");
        if (preloader) {
            gsap.to(preloader, {
                opacity: 0,
                duration: 0.8,
                onComplete: () => preloader.style.display = "none"
            });
        }
    }

    if (document.readyState === "complete") {
        removePreloader();
    } else {
        window.addEventListener("load", removePreloader);
    }

    // Safety timeout for preloader
    setTimeout(removePreloader, 3000);

    const tooltips = [
        "Inicio",
        "Noticia",
        "Ranking"
    ];
    console.log("Modern Template: Init. Sections found:", sections.length);
    const TEMPLATE_IMG_PATH = window.TEMPLATE ? window.TEMPLATE.img : "";
    if ('scrollRestoration' in history) history.scrollRestoration = 'manual';
    window.scrollTo(0, 0);

    let currentdot = 0;
    let isAnimating = false;
    let currentTimeline = null;

    // ── Setup inicial (Hide all staggered children) ──
    sections.forEach((section, i) => {
        const content = section.querySelector(".panel-content");

        if (i === 0) {
            section.classList.add("is-active");
            gsap.set(section, { zIndex: 2, clipPath: "inset(0% 0% 0% 0%)" });
            gsap.set(content, { opacity: 1, y: 0, scale: 1 });
            updateFooterVisibility(section);
        } else {
            gsap.set(section, { zIndex: 1, clipPath: "inset(100% 0% 0% 0%)" });
            gsap.set(content, { opacity: 0, y: 0, scale: 1 }); 
        }

        // Always hide children with the unified "Premium Reveal" starting state for ALL panels
        const animationElements = section.querySelectorAll(".fade-in-up, .fade-in-down, .fade-in-left, .fade-in-right");
        gsap.set(animationElements, { 
            y: 20,
            opacity: 0,
            scale: 0.98,
            transformOrigin: "center center"
        });
    });

// ── Ir a sección por hash si existe ──
const hash = window.location.hash.replace("#", "");

if (hash) {
    const targetIndex = Array.from(sections).findIndex(
        section => section.id === hash
    );

    if (targetIndex !== -1) {
        currentdot = targetIndex;

        sections.forEach((section, i) => {
            section.classList.remove("is-active");

            if (i === targetIndex) {
                section.classList.add("is-active");
                gsap.set(section, { 
                    zIndex: 2, 
                    clipPath: "inset(0% 0% 0% 0%)" 
                });

                const content = section.querySelector(".panel-content");
                if (content) {
                    gsap.set(content, { opacity: 1, y: 0 });
                }
            } else {
                gsap.set(section, { 
                    zIndex: 1, 
                    clipPath: "inset(100% 0% 0% 0%)" 
                });

                const content = section.querySelector(".panel-content");
                if (content) {
                    gsap.set(content, { opacity: 0, y: 60 });
                }
            }
        });

        updateDots();
        updateFooterVisibility(sections[targetIndex]);
    }
}  

    const icons = [
        "fas fa-home",        // Inicio
        "far fa-newspaper",   // Noticia
        "fas fa-trophy"       // Ranking
    ];

    // ── Crear dots ──
    sections.forEach((_, i) => {
        const dot = document.createElement("button");
        dot.className = "nav-dot" + (i === 0 ? " active" : "");
        dot.dataset.index = i;
        dot.setAttribute("aria-label", `Ir a sección ${i + 1}`);

        dot.setAttribute("data-bs-toggle", "tooltip");
        dot.setAttribute("data-bs-placement", "right");
        dot.setAttribute("data-bs-title", tooltips[i]);

        const icon = document.createElement("i");
        icon.className = icons[i] + " dot-icon";
        dot.appendChild(icon);

        dot.addEventListener("click", () => {
            if (!isAnimating) goToSection(i);
        });

        nav.appendChild(dot);
    });

    // 🔥 INICIALIZAR TOOLTIP DESPUÉS DE CREARLOS
    document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
        new bootstrap.Tooltip(el, {
            container: 'body'
        });
    });

    // 🔥 TRIGER INITIAL ANIMATION for the starting section
    setTimeout(() => {
        animatePanel(sections[currentdot]);
    }, 200);

    function updateDots() {
        nav.querySelectorAll(".nav-dot").forEach((dot, i) => {
            dot.classList.toggle("active", i === currentdot);
        });
    }
    function animatePanel(panel) {
        if (!panel) return null;
        
        const allElements = panel.querySelectorAll(".fade-in-up, .fade-in-down, .fade-in-left, .fade-in-right");
        const animationElements = Array.from(allElements).filter(el => !el.closest('.swiper-slide-duplicate'));
        
        if (animationElements.length === 0) return null;

        const tl = gsap.timeline();

        gsap.set(animationElements, { 
            y: 28,
            opacity: 0,
            scale: 0.98,
            transformOrigin: "center center"
        });
        
        tl.to(animationElements, { 
            y: 0, 
            opacity: 1, 
            scale: 1,
            duration: 0.65,
            stagger: 0.06,
            ease: "power3.out"
        });

        return tl;
    }


    function goToSection(index) {
        console.log("Attempting to go to section:", index);
        if (index < 0 || index >= sections.length) {
            console.log("Index out of bounds:", index);
            return;
        }
        if (index === currentdot) return;
        if (isAnimating) {
            console.log("Animation in progress, skipping...");
            return;
        }

        isAnimating = true;
        console.log("Navigating from", currentdot, "to", index);

        const fromIndex = currentdot;
        const from = sections[fromIndex];
        const to = sections[index];
        const toContent = to.querySelector(".panel-content");
        const goingDown = index > fromIndex;

        if (currentTimeline) {
            currentTimeline.kill();
            currentTimeline = null;
        }

        const tl = gsap.timeline({
            onComplete: () => {
                from.classList.remove("is-active");
                to.classList.add("is-active");

                // Cleanup states
                gsap.set(from, { zIndex: 0, clipPath: "inset(100% 0% 0% 0%)" });
                gsap.set(to, { zIndex: 2, clipPath: "inset(0% 0% 0% 0%)" });

                history.replaceState(null, null, `#${to.id}`);
                currentdot = index;
                updateDots();
                updateFooterVisibility(to);

                currentTimeline = null;
                isAnimating = false;
            }
        });
        
        window.goToSection = goToSection;

        // ── Perfect Overlap Transition ──
        gsap.set(to, { 
            zIndex: 3, 
            clipPath: goingDown ? "inset(100% 0% 0% 0%)" : "inset(0% 0% 100% 0%)" 
        });
        
        const clipDuration = 0.8;
        const revealDuration = 0.6;

        // Final cleanup of any properties that might cause jumping
        gsap.to(toContent, { 
            opacity: 1, 
            y: 0, 
            duration: revealDuration, 
            ease: "power2.out", 
            delay: clipDuration * 0.4,
            clearProps: "all" // Aggressive cleanup to restore CSS control
        });

        // Slide the curtains
        tl.to(to, {
            clipPath: "inset(0% 0% 0% 0%)",
            duration: 0.8,
            ease: "power4.inOut"
        }, 0);

        tl.to(from, {
            clipPath: goingDown ? "inset(0% 0% 100% 0%)" : "inset(100% 0% 0% 0%)",
            duration: 0.8,
            ease: "power4.inOut"
        }, 0);

        // 🚀 MASTER SYNC: Trigger children animation for the "to" panel
        tl.add(() => {
            animatePanel(to);
        }, 0.25);

        currentTimeline = tl;
    }

    // 🚀 EXPOSURE: Make it global so templates can call it
    window.goToSection = goToSection;

    // ── Wheel ──
    window.addEventListener("wheel", (e) => {
        if (isAnimating) {
            e.preventDefault();
            return;
        }

        const goingDown = e.deltaY > 0;
        const canScroll = goingDown ? (currentdot < sections.length - 1) : (currentdot > 0);

        if (canScroll) {
            e.preventDefault();
            goToSection(goingDown ? currentdot + 1 : currentdot - 1);
        }
        // If they are at the end and scrolling down, or start and scrolling up, 
        // DO NOT preventDefault to allow normal browser behavior if necessary
    }, { passive: false });

    // ── Touch ──
    let touchStartY = 0;

    window.addEventListener("touchstart", (e) => {
        touchStartY = e.touches[0].clientY;
    }, { passive: true });

    window.addEventListener("touchmove", (e) => {
        if (isAnimating) {
            e.preventDefault();
            return;
        }
        // Only prevent default if we are within bounds and intend to scroll between panels
        // This is tricky with touch, so we usually keep it simple for panels
        e.preventDefault();
    }, { passive: false });

    window.addEventListener("touchend", (e) => {
        if (isAnimating) return;

        const diff = touchStartY - e.changedTouches[0].clientY;

        if (diff > 50) {
            goToSection(currentdot + 1);
        } else if (diff < -50) {
            goToSection(currentdot - 1);
        }
    });

    // ── Keyboard ──
    window.addEventListener("keydown", (e) => {
        if (isAnimating) return;

        if (e.key === "ArrowDown" || e.key === "PageDown") {
            e.preventDefault();
            goToSection(currentdot + 1);
        } else if (e.key === "ArrowUp" || e.key === "PageUp") {
            e.preventDefault();
            goToSection(currentdot - 1);
        }
    });

    window.addEventListener("hashchange", () => {
        const hash = window.location.hash.replace("#", "");
        if (hash) {
            const targetIndex = Array.from(sections).findIndex(section => section.id === hash);
            if (targetIndex !== -1 && targetIndex !== currentdot) {
                goToSection(targetIndex);
            }
        }
    });

    const classes = [
        {
            title: "DARK KNIGHT",
            desc: "Guerrero especializado en combate cuerpo a cuerpo. Alta resistencia, gran poder físico y excelente desempeño en PvP frontal.",
            img: TEMPLATE_IMG_PATH + "classesweb/knight-bg.png",
            bg: TEMPLATE_IMG_PATH + "classesweb/knight-bg.png",
            stats: { str: "80%", agi: "50%", vit: "90%", ene: "20%", cmd: null }
        },
        {
            title: "DARK WIZARD",
            desc: "Maestro de las artes místicas. Controla los elementos para desatar daño mágico masivo en área, ideal para aniquilar múltiples enemigos.",
            img: TEMPLATE_IMG_PATH + "classesweb/wizard-bg.png",
            bg: TEMPLATE_IMG_PATH + "classesweb/wizard-bg.png",
            stats: { str: "20%", agi: "60%", vit: "40%", ene: "95%", cmd: null }
        },
        {
            title: "FAIRY ELF",
            desc: "Arquera ágil y versátil. Experta en daño a distancia, invocación de criaturas y soporte con potentes curaciones y buffs aliados.",
            img: TEMPLATE_IMG_PATH + "classesweb/fairy-bg.png",
            bg: TEMPLATE_IMG_PATH + "classesweb/fairy-bg.png",
            stats: { str: "30%", agi: "90%", vit: "40%", ene: "70%", cmd: null }
        },
        {
            title: "MAGIC GLADIATOR",
            desc: "Guerrero híbrido que domina tanto el daño físico como el mágico. Muy versátil en combate con la habilidad de usar armamento de Knight y Wizard.",
            img: TEMPLATE_IMG_PATH + "classesweb/magical-bg.png",
            bg: TEMPLATE_IMG_PATH + "classesweb/magical-bg.png",
            stats: { str: "70%", agi: "60%", vit: "50%", ene: "70%", cmd: null }
        },
        {
            title: "DARK LORD",
            desc: "Poderoso líder nato. Usa daño crítico, monturas especiales (Dark Horse/Raven) y comando exclusivo para potenciar clanes impresionantes.",
            img: TEMPLATE_IMG_PATH + "classesweb/darklord-bg.png",
            bg: TEMPLATE_IMG_PATH + "classesweb/darklord-bg.png",
            stats: { str: "85%", agi: "50%", vit: "60%", ene: "40%", cmd: "90%" }
        },
        {
            title: "SUMMONER",
            desc: "Hechicera oscura que invoca energías prohibidas. Especialista en daño mágico continuo (curse), robo de vida y debilitamiento de enemigos.",
            img: TEMPLATE_IMG_PATH + "classesweb/summoner-bg.png",
            bg: TEMPLATE_IMG_PATH + "classesweb/summoner-bg.png",
            stats: { str: "25%", agi: "65%", vit: "45%", ene: "90%", cmd: null }
        },
        {
            title: "RAGE FIGHTER",
            desc: "Luchador implacable enfocado en el combate a puño limpio. Ignora defensa del enemigo, con altísima vida para aguantar la primera línea de fuego.",
            img: TEMPLATE_IMG_PATH + "classesweb/ragefighter-bg.png",
            bg: TEMPLATE_IMG_PATH + "classesweb/ragefighter-bg.png",
            stats: { str: "60%", agi: "75%", vit: "95%", ene: "15%", cmd: null }
        }
    ];

    let current = 0;
    let autoRotationInterval = null;
    const ROTATION_SPEED = 7000;

    const title = document.getElementById("class-title");
    const img = document.getElementById("class-character-img");
    const desc = document.getElementById("class-description");
    const bg = document.querySelector(".class-bg");

    let statsChart = null;

    function getChartDataForClass(classData) {
        if (!classData || !classData.stats) return [0, 0, 0, 0, 0];
        return [
            parseInt(classData.stats.str) || 0,
            parseInt(classData.stats.agi) || 0,
            parseInt(classData.stats.vit) || 0,
            parseInt(classData.stats.ene) || 0,
            classData.stats.cmd ? (parseInt(classData.stats.cmd) || 0) : 0
        ];
    }

    function initChart(classData) {
        const canvas = document.getElementById('classStatsChart');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        if (!ctx) return;
        
        const gradient = ctx.createRadialGradient(
            ctx.canvas.width / 2, ctx.canvas.height / 2, 0,
            ctx.canvas.width / 2, ctx.canvas.height / 2, 150
        );
        gradient.addColorStop(0, 'rgba(232, 163, 79, 0.4)');
        gradient.addColorStop(1, 'rgba(232, 163, 79, 0.05)');

        const data = {
            labels: ['FUERZA', 'AGILIDAD', 'VITALIDAD', 'ENERGÍA', 'COMANDO'],
            datasets: [{
                label: 'Estadísticas',
                data: getChartDataForClass(classData),
                fill: true,
                backgroundColor: gradient,
                borderColor: '#e8a34f',
                borderWidth: 3,
                pointBackgroundColor: '#fff',
                pointBorderColor: '#e8a34f',
                pointBorderWidth: 2,
                pointRadius: 4,
                tension: 0.1
            }]
        };

        statsChart = new Chart(ctx, {
            type: 'radar',
            data: data,
            options: {
                maintainAspectRatio: false,
                responsive: true,
                animation: { duration: 1500, easing: 'easeOutQuart' },
                scales: {
                    r: {
                        angleLines: { color: 'rgba(255, 215, 0, 0.4)', lineWidth: 2 },
                        grid: { color: 'rgba(255, 215, 0, 0.25)', lineWidth: 1, circular: true },
                        pointLabels: { 
                            color: '#e8a34f', 
                            font: { size: 12, family: "'Merriweather', serif", weight: 'bold' },
                            padding: 15
                        },
                        ticks: { display: false, stepSize: 20 },
                        suggestedMin: 0,
                        suggestedMax: 100
                    }
                },
                plugins: { legend: { display: false } }
            }
        });
    }

    function updateClass() {
        if (!title || !img || !desc) return;

        const classData = classes[current];
        const tl = gsap.timeline();

        // 1. Salida
        tl.to([title, desc, "#classStatsChart", img], { opacity: 0, duration: 0.4 });

        // 2. Cambio
        tl.add(() => {
            title.textContent = classData.title;
            img.src = classData.img;
            desc.innerHTML = `<div class="class-desc-text">${classData.desc}</div>`;

            const chartData = getChartDataForClass(classData);
            if (!statsChart) {
                initChart(classData);
            } else {
                statsChart.data.datasets[0].data = chartData;
                statsChart.update();
            }
        });

        // 3. Entrada
        tl.fromTo(title, { x: -80, opacity: 0 }, { x: 0, opacity: 1, duration: 0.6, ease: "power3.out" });
        tl.fromTo(img, { opacity: 0 }, { opacity: 1, duration: 1.0, ease: "power2.inOut" }, "-=0.4");
        tl.fromTo(desc, { y: 20, opacity: 0 }, { y: 0, opacity: 1, duration: 0.6, ease: "power2.out" }, "-=0.5");
        tl.to("#classStatsChart", { opacity: 1, duration: 0.6 }, "-=0.3");
    }

    function startAutoRotation() {
        stopAutoRotation();
        autoRotationInterval = setInterval(() => {
            current = (current + 1) % classes.length;
            updateClass();
        }, ROTATION_SPEED);
    }

    function stopAutoRotation() {
        if (autoRotationInterval) {
            clearInterval(autoRotationInterval);
            autoRotationInterval = null;
        }
    }

    updateClass();
    startAutoRotation();

    // Never pause class rotation on hover
    const showcasePanel = document.getElementById("class-showcase") || document.getElementById("clase");
    if (showcasePanel) {
        showcasePanel.addEventListener("mouseenter", startAutoRotation);
        showcasePanel.addEventListener("mousemove", startAutoRotation);
        showcasePanel.addEventListener("mouseleave", startAutoRotation);
    }

    const newsItemCount = document.querySelectorAll(".newsSwiper .swiper-slide").length;
    const rankingsItemCount = document.querySelectorAll(".rankingsSwiper .swiper-slide").length;

    const swiperNews = new Swiper(".newsSwiper", {
        slidesPerView: 4,
        spaceBetween: 25,
        grabCursor: true,
        loop: newsItemCount > 4,
        // Center when there are fewer slides than slidesPerView (without breaking translate math)
        centeredSlides: false,
        centerInsufficientSlides: true,
        centeredSlidesBounds: true,
        rewind: true,
        watchOverflow: true,
        observer: true,
        observeParents: true,
        autoplay: (newsItemCount > 1) ? { delay: 7000, disableOnInteraction: false, pauseOnMouseEnter: true } : false,
        pagination: { el: ".newsSwiper .swiper-pagination", clickable: true },
        navigation: { nextEl: ".newsSwiper .swiper-button-next", prevEl: ".newsSwiper .swiper-button-prev" },
        breakpoints: {
            0: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            992: { slidesPerView: 3 },
            1200: { slidesPerView: 4 }
        }
    });

    const swiperRankings = new Swiper(".rankingsSwiper", {
        slidesPerView: 4,
        spaceBetween: 25,
        grabCursor: true,
        loop: rankingsItemCount > 4,
        centeredSlides: false,
        centerInsufficientSlides: true,
        centeredSlidesBounds: true,
        rewind: true,
        watchOverflow: true,
        observer: true,
        observeParents: true,
        autoplay: (rankingsItemCount > 1) ? { delay: 6000, disableOnInteraction: false, pauseOnMouseEnter: true } : false,
        pagination: {
            el: ".rankingsSwiper .swiper-pagination",
            clickable: true,
            dynamicBullets: true
        },
        navigation: { nextEl: ".rankingsSwiper .swiper-button-next", prevEl: ".rankingsSwiper .swiper-button-prev" },
        breakpoints: {
            0: { slidesPerView: 1 },
            768: { slidesPerView: 2 },
            992: { slidesPerView: 3 },
            1200: { slidesPerView: 4 }
        }
    }); // Close swiperRankings

    // Final check for initial panel animation (after Swiper/hash)
    const initialPanel = document.querySelector(".panel.is-active");
    if (initialPanel) {
        setTimeout(() => {
            animatePanel(initialPanel);
        }, 500); // 500ms allows Swiper and other scripts to settle
    }
}); // Close DOMContentLoaded


var csTime = {
	csDays: null,
	csHours: null,
	csMinutes: null,
	csSeconds: null,
	csTimeLeft: null,
	csNextStageTimeLeft: null,
	battleMode: false,
	days_module: null,
	hours_module: null,
	minutes_module: null,
	init: function() {
		var a = this;
		$.getJSON(baseUrl + "api/castlesiege.php", function(c) {
			a.csTimeLeft = c.TimeLeft;
			a.csNextStageTimeLeft = c.NextStageTimeLeft;
			setInterval(function() {
				a.update();
			}, 1000)
		})
	},
	update: function() {
		var b = this;
		b.csTimeLeft = b.csTimeLeft-1;
		b.csNextStageTimeLeft = b.csNextStageTimeLeft-1;
		
		if(b.csTimeLeft >= 1) {
			b.days_module = b.csTimeLeft % 86400;
			b.csDays = (b.csTimeLeft-b.days_module)/86400;
			b.hours_module = b.days_module % 3600;
			b.csHours = (b.days_module-b.hours_module)/3600;
			b.minutes_module = b.hours_module % 60;
			b.csMinutes = (b.hours_module-b.minutes_module)/60;
			b.csSeconds = b.minutes_module;
		} else {
			b.battleMode = true;
			b.csDays = 0;
			b.csHours = 0;
			b.csMinutes = 0;
			b.csSeconds = 0;
		}
		
		if(b.battleMode == true) {
			if($('#cscountdown').length) {
				document.getElementById("cscountdown").innerHTML = 'Battle';
			}
			if($('#siegeTimer').length) {
				document.getElementById("siegeTimer").innerHTML = 'Battle';
			}
		} else {
			
			var countdown = '';
			if(b.csTimeLeft > 86400) countdown += b.csDays + "<span>d</span> ";
			if(b.csTimeLeft > 3600) countdown += b.csHours + "<span>h</span> ";
			if(b.csTimeLeft > 60) countdown += b.csMinutes + "<span>m</span> ";
			countdown += b.csSeconds + "<span>s</span>";
			
			if($('#cscountdown').length) {
				document.getElementById("cscountdown").innerHTML = countdown;
			}
			if($('#siegeTimer').length) {
				document.getElementById("siegeTimer").innerHTML = countdown;
			}
		}
	}
};


function rankingsFilterByClass() {
	var delay = 500; // milliseconds
	var classList = new Array();
	
	for(var i = 0; i < arguments.length; i++) {
		classList[i] = arguments[i];
	}
	
	if($(".rankings-table").length) {
		$(".rankings-table").fadeOut().delay(delay).fadeIn();
		setTimeout(function() {
			$(".rankings-table tr").each(function() {
				if($(this).attr("data-class-id") == null) { return true; }
				if(classList.includes(parseInt($(this).attr("data-class-id"))) == false) {
					$(this).hide();
				} else {
					$(this).show();
				}
			});
		}, delay);
	}
}

function rankingsFilterRemove() {
	var delay = 500; // milliseconds
	
	$(".rankings-table").fadeOut().delay(delay).fadeIn();
	setTimeout(function() {
		if($(".rankings-table").length) {
			$(".rankings-table tr").each(function() {
					$(this).fadeIn();
				}
			);
		}
	}, delay);
}

$(function() {
	if($(".rankings-class-filter-selection").length) {
		$('a.rankings-class-filter-selection').click(function(){
			$('a.rankings-class-filter-selection').addClass("rankings-class-filter-grayscale");
			$(this).removeClass("rankings-class-filter-grayscale");
		});
	}
	// Discord Hero Widget (avatars + online count)
	initDiscordHeroWidget();
});

function initDiscordHeroWidget() {
	var container = document.getElementById('discordAvatars');
	var countEl = document.getElementById('discordOnlineCount');
	var loadingEl = document.getElementById('discordLoading');
	if (!container || !countEl) return;
	var guildId = '1368641757776052347';
	var apiUrl = (typeof baseUrl !== 'undefined' ? baseUrl : '') + 'api/discord_widget.php?guild_id=' + guildId;
	fetch(apiUrl)
		.then(function(r) { return r.json(); })
		.then(function(data) {
			if (loadingEl) loadingEl.remove();
			var members = data.members || [];
			var presence = data.presence_count || members.length;
			countEl.textContent = presence;
			var maxAvatars = 10;
			var shown = members.slice(0, maxAvatars);
			shown.forEach(function(m) {
				var img = document.createElement('img');
				img.className = 'discord-hero-avatar';
				img.src = m.avatar_url || '';
				img.alt = m.username || '';
				img.title = m.username || '';
				img.loading = 'lazy';
				img.onerror = function() { this.style.display = 'none'; };
				container.appendChild(img);
			});
			if (shown.length === 0) {
				var empty = document.createElement('span');
				empty.className = 'discord-hero-loading';
				empty.textContent = 'Sin miembros visibles';
				container.appendChild(empty);
			}
		})
		.catch(function() {
			if (loadingEl) loadingEl.textContent = 'Error al cargar';
			countEl.textContent = '—';
		});
}


