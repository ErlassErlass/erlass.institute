/**
 * Erlass Institute - Onboarding Tour Engine (Driver.js)
 */

import { driver } from 'driver.js';
import { instructorTourSteps } from './instructor-tour.js';
import { adminTourSteps } from './admin-tour.js';

class ErlassOnboardingEngine {
    constructor() {
        this.driverObj = null;
        this.currentRole = this.detectUserRole();
        this.storageKey = `erlass_tour_seen_${this.currentRole}`;
    }

    /**
     * Detect user role from meta tag or root element
     */
    detectUserRole() {
        const metaRole = document.querySelector('meta[name="user-role"]');
        if (metaRole && metaRole.getAttribute('content')) {
            return metaRole.getAttribute('content').toLowerCase();
        }
        return 'instruktur';
    }

    /**
     * Initialize Driver.js configuration
     */
    initDriver(steps) {
        // Filter steps to only include elements that exist in current DOM
        const activeSteps = steps.filter(step => {
            if (!step.element) return true;
            const el = document.querySelector(step.element);
            return el && el.offsetParent !== null; // Element is visible
        });

        if (activeSteps.length === 0) {
            console.warn('[Erlass Tour] No visible tour target elements found on this page.');
            return null;
        }

        return driver({
            showProgress: true,
            animate: true,
            allowClose: true,
            overlayOpacity: 0.7,
            stagePadding: 6,
            stageRadius: 14,
            popoverClass: 'erlass-tour-popover',
            nextBtnText: 'Lanjut →',
            prevBtnText: '← Kembali',
            doneBtnText: 'Selesai ✨',
            progressText: '{{current}} dari {{total}}',
            steps: activeSteps,
            onDestroyStarted: () => {
                this.markAsCompleted();
                this.driverObj?.destroy();
            }
        });
    }

    /**
     * Start tour according to user role or specific override
     */
    startTour(forcedRole = null) {
        const role = forcedRole || this.currentRole;
        let steps = [];

        if (role === 'instruktur') {
            steps = instructorTourSteps;
        } else {
            steps = adminTourSteps;
        }

        this.driverObj = this.initDriver(steps);
        if (this.driverObj) {
            this.driverObj.drive();
        }
    }

    /**
     * Mark tour as completed in localStorage
     */
    markAsCompleted() {
        try {
            localStorage.setItem(this.storageKey, 'true');
        } catch (e) {
            // Ignore localStorage errors
        }
    }

    /**
     * Reset tour status so it can trigger automatically again
     */
    resetTour() {
        try {
            localStorage.removeItem(this.storageKey);
        } catch (e) {}
    }

    /**
     * Auto check and prompt on first visit
     */
    checkAutoStart() {
        // Only run auto-tour on dashboard page
        if (!window.location.pathname.includes('/dashboard') && window.location.pathname !== '/') {
            return;
        }

        let hasSeen = false;
        try {
            hasSeen = localStorage.getItem(this.storageKey) === 'true';
        } catch (e) {}

        if (!hasSeen) {
            // Give a tiny delay for page rendering & animations
            setTimeout(() => {
                this.startTour();
            }, 800);
        }
    }

    /**
     * Bind DOM button listeners
     */
    bindTriggers() {
        // Any button with class or id
        document.querySelectorAll('.btn-trigger-erlass-tour, #btnStartDashboardTour').forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.preventDefault();
                const specificRole = btn.dataset.tourRole || null;
                this.startTour(specificRole);
            });
        });
    }
}

// Global initialization
export function initErlassOnboarding() {
    const onboarding = new ErlassOnboardingEngine();
    window.ErlassOnboarding = onboarding;
    
    // Bind click events
    onboarding.bindTriggers();
    
    // Check first visit auto-run
    onboarding.checkAutoStart();
}
