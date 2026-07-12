"""Checks for the combat math: hit chance, armour class, damage, and rounds to kill."""

import math

import combat


def test_hit_probability_matched_unclothed():
    # Unclothed AC 28 (base 18 + Dexterity 10) against a matched +10 attacker.
    assert math.isclose(combat.hit_probability(10, 28), 0.624, abs_tol=0.002)


def test_hit_probability_clamps():
    assert combat.hit_probability(20, 18) == 1.0  # target below the dice floor
    assert combat.hit_probability(0, 40) == 0.0  # target above the dice ceiling


def test_armour_class_from_values():
    assert combat.armour_class(18, 10, 1) == 28  # unclothed: base 18 + full Dexterity 10
    assert combat.armour_class(22, 10, 2) == 27  # mail: base 22 + half of Dexterity 10


def test_average_damage_and_step_down():
    assert combat.average_damage(3, 10) == 16.5
    assert combat.average_damage(3, 6) == 10.5


def test_rounds_to_kill_with_multiplier():
    assert combat.rounds_to_kill(30, 3, 10, 1.0) == 2  # 30 / 16.5
    assert combat.rounds_to_kill(30, 3, 10, 0.5) == 4  # resisted: 30 / 8.25
    assert combat.rounds_to_kill(30, 6, 10, 2.0) == 1  # vulnerable: 30 / 66


def test_aoe_breakeven():
    assert combat.aoe_breakeven(10, 6) == 2
