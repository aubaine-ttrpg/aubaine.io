"""Combat math for Aubaine: armour class, damage from Energy, and rounds to kill.

Hit chance reads the committed 3d12 keep-3 distributions from the dice-and-modifiers domain, so
this domain depends on that one only through data, never through code.
"""

from __future__ import annotations

import json
import math
from pathlib import Path

ROLL_DISTRIBUTIONS = (
    Path(__file__).resolve().parents[2]
    / "dice-and-modifiers"
    / "data"
    / "roll-distributions.json"
)

DIE_AVERAGE = {10: 5.5, 8: 4.5, 6: 3.5}


def _cdf_at_least(net: int = 0) -> dict[int, float]:
    data = json.loads(ROLL_DISTRIBUTIONS.read_text(encoding="utf-8"))
    return {int(k): v for k, v in data[str(net)]["cdf_at_least"].items()}


def hit_probability(attacker_bonus: int, ac: int, net: int = 0) -> float:
    """P(a keep-3 roll plus the attacker's bonus meets or beats the target AC)."""
    cdf = _cdf_at_least(net)
    target = ac - attacker_bonus
    low, high = min(cdf), max(cdf)
    if target <= low:
        return 1.0
    if target > high:
        return 0.0
    return cdf[target]


def armour_class(base: int, stat_value: int, per: int) -> int:
    """AC = base + floor(the wearer's scaling characteristic value / per)."""
    return base + stat_value // per


def average_damage(energy: int, die: int = 10, multiplier: float = 1.0) -> float:
    """Average damage of ``energy`` dice of size ``die``, after a resist/vuln multiplier."""
    return energy * DIE_AVERAGE[die] * multiplier


def rounds_to_kill(hp: int, energy: int, die: int = 10, multiplier: float = 1.0) -> int:
    dmg = average_damage(energy, die, multiplier)
    return math.ceil(hp / dmg) if dmg > 0 else 0


def aoe_breakeven(single_die: int = 10, area_die: int = 6) -> int:
    """Smallest target count at which stepped-down area dice out-total a single-target hit.

    Damage is linear in the die average, so the count does not depend on the Energy spent.
    """
    ratio = DIE_AVERAGE[single_die] / DIE_AVERAGE[area_die]
    return math.floor(ratio) + 1
