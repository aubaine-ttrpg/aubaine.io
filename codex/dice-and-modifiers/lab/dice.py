"""Exact resolution math for Aubaine's 3d12 keep-3 engine.

Every distribution here is exact (rational, via ``fractions.Fraction``): it enumerates the
multisets of dice faces and weights each by its multinomial multiplicity, so nothing is
sampled. The counts for ``n`` dice always sum to ``12 ** n``.
"""

from __future__ import annotations

from collections import defaultdict
from fractions import Fraction
from itertools import combinations_with_replacement
from math import comb, factorial

FACES = 12
KEEP = 3


def _multiplicity(sorted_faces: tuple[int, ...]) -> int:
    """How many ordered rolls collapse to this sorted multiset of faces."""
    m = factorial(len(sorted_faces))
    run = 0
    prev = None
    for face in sorted_faces:
        run = run + 1 if face == prev else 1
        prev = face
        m //= run
    return m


def keep_sum_distribution(
    n_dice: int = KEEP, keep: int = KEEP, mode: str = "best", faces: int = FACES
) -> dict[int, Fraction]:
    """Exact pmf of summing the ``keep`` best (or worst) of ``n_dice`` d``faces``."""
    if n_dice < keep:
        raise ValueError("n_dice must be >= keep")
    if mode not in ("best", "worst"):
        raise ValueError("mode must be 'best' or 'worst'")
    total = Fraction(faces**n_dice)
    counts: dict[int, int] = defaultdict(int)
    for combo in combinations_with_replacement(range(1, faces + 1), n_dice):
        kept = combo[-keep:] if mode == "best" else combo[:keep]
        counts[sum(kept)] += _multiplicity(combo)
    return {s: Fraction(c) / total for s, c in sorted(counts.items())}


def roll_distribution(av: int = 0, dv: int = 0) -> dict[int, Fraction]:
    """Distribution of the kept sum after resolving ``av`` advantages vs ``dv`` disadvantages.

    Advantage and disadvantage cancel one for one. A positive net keeps the best 3 of ``3+net``
    dice; a negative net keeps the worst 3.
    """
    net = av - dv
    if net >= 0:
        return keep_sum_distribution(KEEP + net, KEEP, "best")
    return keep_sum_distribution(KEEP - net, KEEP, "worst")


def distribution_mean(dist: dict[int, Fraction]) -> float:
    return float(sum(s * p for s, p in dist.items()))


def distribution_stdev(dist: dict[int, Fraction]) -> float:
    mu = sum(s * p for s, p in dist.items())
    var = sum((s - mu) ** 2 * p for s, p in dist.items())
    return float(var) ** 0.5


def cdf_at_least(dist: dict[int, Fraction]) -> dict[int, Fraction]:
    """Map each attainable sum ``s`` to P(kept sum >= s)."""
    result: dict[int, Fraction] = {}
    running = Fraction(0)
    for s in sorted(dist, reverse=True):
        running += dist[s]
        result[s] = running
    return dict(sorted(result.items()))


def p_at_least(dist: dict[int, Fraction], target: int) -> Fraction:
    return sum((p for s, p in dist.items() if s >= target), Fraction(0))


def p_success(target: int, modifier: int = 0, av: int = 0, dv: int = 0) -> float:
    """P(kept sum + modifier >= target)."""
    return float(p_at_least(roll_distribution(av, dv), target - modifier))


def _p_at_least_k_faces(n: int, k: int, p: Fraction) -> Fraction:
    """P(at least ``k`` of ``n`` independent dice show a face with probability ``p``)."""
    q = 1 - p
    return sum((comb(n, i) * p**i * q ** (n - i) for i in range(k, n + 1)), Fraction(0))


def crit_success(av: int = 0, dv: int = 0, faces: int = FACES) -> Fraction:
    """P(the 3 kept dice all show the maximum face)."""
    net = av - dv
    n = KEEP + abs(net)
    p = Fraction(1, faces)
    if net >= 0:  # keeping the best 3: needs at least 3 maxima among n dice
        return _p_at_least_k_faces(n, KEEP, p)
    return p**n  # keeping the worst 3: needs every die to be a maximum


def crit_fail(av: int = 0, dv: int = 0, faces: int = FACES) -> Fraction:
    """P(the 3 kept dice all show a 1)."""
    net = av - dv
    n = KEEP + abs(net)
    p = Fraction(1, faces)
    if net <= 0:  # keeping the worst 3: needs at least 3 ones among n dice
        return _p_at_least_k_faces(n, KEEP, p)
    return p**n  # keeping the best 3: needs every die to be a 1


def opposed_diff_distribution(
    av_a: int = 0, dv_a: int = 0, av_b: int = 0, dv_b: int = 0
) -> dict[int, Fraction]:
    """Distribution of (A's kept sum - B's kept sum)."""
    da = roll_distribution(av_a, dv_a)
    db = roll_distribution(av_b, dv_b)
    diff: dict[int, Fraction] = defaultdict(Fraction)
    for sa, pa in da.items():
        for sb, pb in db.items():
            diff[sa - sb] += pa * pb
    return dict(sorted(diff.items()))


def opposed_outcome(
    mod_edge: int = 0, av_a: int = 0, dv_a: int = 0, av_b: int = 0, dv_b: int = 0
) -> tuple[float, float, float]:
    """Return (A strictly wins, tie, B strictly wins) given A's modifier edge over B.

    Ties are resolved elsewhere (higher modifier, or a critical); this reports the raw dice
    outcome so the swinginess and tie mass are visible.
    """
    diff = opposed_diff_distribution(av_a, dv_a, av_b, dv_b)
    win = sum((p for d, p in diff.items() if d + mod_edge > 0), Fraction(0))
    tie = sum((p for d, p in diff.items() if d + mod_edge == 0), Fraction(0))
    lose = 1 - win - tie
    return float(win), float(tie), float(lose)
