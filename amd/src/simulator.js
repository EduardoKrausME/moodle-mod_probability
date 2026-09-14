// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * simulator.js
 *
 * @package   mod_probability
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define(["jquery", "core/templates", "core/str"], function($, Templates, Str) {
    "use strict";

    const SELECTORS = {
        root: "[data-region='probability-simulator']",
        mode: "[data-mode]",
        config: "[data-config]",
        trial: "[data-trials]",
        run: "[data-action='run']",
        results: "[data-region='results']",
        error: "[data-region='error']",
    };

    const randomFloat = function() {
        if (window.crypto && window.crypto.getRandomValues) {
            const values = new Uint32Array(1);
            window.crypto.getRandomValues(values);
            return values[0] / 4294967296;
        }
        return Math.random();
    };

    const getSelectedMode = function(root) {
        return root.find(SELECTORS.mode + "[aria-pressed='true']").data("mode");
    };

    const getSelectedTrials = function(root) {
        return Number(root.find(SELECTORS.trial + "[aria-pressed='true']").data("trials"));
    };

    const setPressed = function(elements, selected) {
        elements.attr("aria-pressed", "false");
        selected.attr("aria-pressed", "true");
    };

    const parseUrn = function(value) {
        const outcomes = [];
        String(value).split(/\r?\n/).forEach(function(line) {
            const trimmed = line.trim();
            if (!trimmed) {
                return;
            }
            const parts = trimmed.split("=");
            if (parts.length < 2) {
                return;
            }
            const label = parts.shift().trim();
            const quantity = Number(parts.join("=").trim());
            if (label && Number.isFinite(quantity) && quantity > 0) {
                outcomes.push({label: label, weight: quantity});
            }
        });
        return outcomes;
    };

    const simulate = function(root, mode, trials) {
        let definitions = [];

        if (mode === "coin") {
            const probability = Number(root.find("[data-input='coin-probability']").val());
            if (!Number.isFinite(probability) || probability < 0 || probability > 100) {
                return {errorKey: "invalidcoin"};
            }
            definitions = [
                {labelKey: "heads", weight: probability},
                {labelKey: "tails", weight: 100 - probability},
            ];
        } else if (mode === "die") {
            const sides = Number(root.find("[data-input='die-sides']").val());
            if (!Number.isInteger(sides) || sides < 2 || sides > 100) {
                return {errorKey: "invaliddie"};
            }
            for (let side = 1; side <= sides; side++) {
                definitions.push({label: String(side), weight: 1});
            }
        } else if (mode === "urn") {
            definitions = parseUrn(root.find("[data-input='urn-contents']").val());
            if (!definitions.length) {
                return {errorKey: "invalidurn"};
            }
        }

        return {definitions: definitions, trials: trials};
    };

    const resolveLabels = async function(definitions) {
        const promises = definitions.map(function(item) {
            if (!item.labelKey) {
                return Promise.resolve(item.label);
            }
            return Str.get_string(item.labelKey, "mod_probability");
        });
        return Promise.all(promises);
    };

    const runSimulation = async function(root) {
        const mode = getSelectedMode(root);
        const trials = getSelectedTrials(root);
        const preparation = simulate(root, mode, trials);
        const error = root.find(SELECTORS.error);
        const results = root.find(SELECTORS.results);

        error.attr("hidden", true).empty();

        if (preparation.errorKey) {
            const message = await Str.get_string(preparation.errorKey, "mod_probability");
            error.text(message).removeAttr("hidden");
            results.attr("hidden", true).empty();
            return;
        }

        const definitions = preparation.definitions;
        const labels = await resolveLabels(definitions);
        const totalWeight = definitions.reduce(function(sum, item) {
            return sum + item.weight;
        }, 0);
        const counts = new Array(definitions.length).fill(0);
        const sequence = [];

        for (let index = 0; index < trials; index++) {
            let target = randomFloat() * totalWeight;
            let selected = definitions.length - 1;
            for (let outcome = 0; outcome < definitions.length; outcome++) {
                target -= definitions[outcome].weight;
                if (target < 0) {
                    selected = outcome;
                    break;
                }
            }
            counts[selected]++;
            if (sequence.length < 50) {
                sequence.push(labels[selected]);
            }
        }

        let largestError = 0;
        const rows = definitions.map(function(item, index) {
            const theoretical = item.weight / totalWeight * 100;
            const observed = counts[index] / trials * 100;
            const difference = observed - theoretical;
            largestError = Math.max(largestError, Math.abs(difference));
            return {
                label: labels[index],
                count: counts[index],
                observed: Math.max(0, Math.min(100, observed)),
                observedformatted: observed.toFixed(2),
                theoretical: Math.max(0, Math.min(100, theoretical)),
                theoreticalformatted: theoretical.toFixed(2),
                differenceformatted: (difference >= 0 ? "+" : "") + difference.toFixed(2),
            };
        });

        const context = {
            trials: trials.toLocaleString(),
            outcomecount: rows.length,
            largesterror: largestError.toFixed(2),
            rows: rows,
            sequence: sequence,
        };

        const rendered = await Templates.renderForPromise("mod_probability/results", context);
        Templates.replaceNodeContents(results, rendered.html, rendered.js);
        results.removeAttr("hidden");
    };

    const initRoot = function(root, options) {
        root.on("click", SELECTORS.mode, function() {
            const button = $(this);
            setPressed(root.find(SELECTORS.mode), button);
            root.find(SELECTORS.config).attr("hidden", true);
            root.find("[data-config='" + button.data("mode") + "']").removeAttr("hidden");
            root.find(SELECTORS.results).attr("hidden", true).empty();
            root.find(SELECTORS.error).attr("hidden", true).empty();
        });

        root.on("click", SELECTORS.trial, function() {
            setPressed(root.find(SELECTORS.trial), $(this));
        });

        root.on("click", SELECTORS.run, function() {
            runSimulation(root);
        });

        if (options.defaultTrials) {
            const trial = root.find(SELECTORS.trial + "[data-trials='" + options.defaultTrials + "']");
            if (trial.length) {
                setPressed(root.find(SELECTORS.trial), trial);
            }
        }
    };

    return {
        init: function(options) {
            $(SELECTORS.root).each(function() {
                initRoot($(this), options || {});
            });
        },
    };
});
