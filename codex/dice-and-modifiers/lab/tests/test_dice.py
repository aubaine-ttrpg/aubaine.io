"""Golden tests pinning the verified constants of the 3d12 keep-3 engine."""

import math
from fractions import Fraction

import dice


def test_base_stats():
    base = dice.roll_distribution()
    assert dice.distribution_mean(base) == 19.5
    assert math.isclose(dice.distribution_stdev(base), 5.979, abs_tol=0.001)
    assert min(base) == 3
    assert max(base) == 36


def test_base_probabilities_are_exact():
    base = dice.roll_distribution()
    assert sum(base.values()) == 1
    assert dice.p_at_least(base, 20) == Fraction(1, 2)


def test_base_crit_is_one_in_1728():
    assert dice.crit_success() == Fraction(1, 1728)
    assert dice.crit_fail() == Fraction(1, 1728)


def test_advantage_crit_rates():
    assert dice.crit_success(av=1) == Fraction(5, 2304)  # >=3 twelves among 4 dice
    assert dice.crit_fail(av=1) == Fraction(1, 20736)  # all 4 dice show 1
    assert dice.crit_success(av=2) == Fraction(211, 41472)


def test_disadvantage_mirrors_advantage():
    assert dice.crit_fail(dv=1) == dice.crit_success(av=1)
    assert dice.crit_success(dv=1) == dice.crit_fail(av=1)


def test_advantage_mean_shift():
    av1 = dice.roll_distribution(av=1)
    assert math.isclose(dice.distribution_mean(av1), 23.072, abs_tol=0.01)


def test_keep_distributions_are_normalised():
    for n in range(3, 8):
        assert sum(dice.keep_sum_distribution(n, 3, "best").values()) == 1
        assert sum(dice.keep_sum_distribution(n, 3, "worst").values()) == 1


def test_opposed_variance():
    diff = dice.opposed_diff_distribution()
    mu = sum(d * p for d, p in diff.items())
    var = float(sum((d - mu) ** 2 * p for d, p in diff.items()))
    assert math.isclose(var**0.5, 8.4558, abs_tol=0.001)


def test_flat_dc_success_and_modifier():
    assert math.isclose(dice.p_success(19), 0.5631, abs_tol=0.001)  # Standard at +0
    assert dice.p_success(19, modifier=10) > 0.96  # a skilled character outclasses it
    assert dice.p_success(19, modifier=20) == 1.0  # a maxed character cannot fail it
