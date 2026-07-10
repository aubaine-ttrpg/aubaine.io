from scripts.check_contrast import contrast_ratio, parse_hex_color


def test_parse_hex_color():
    assert parse_hex_color("#FFFFFF") == (255, 255, 255)
    assert parse_hex_color("000000") == (0, 0, 0)


def test_black_white_contrast():
    assert round(contrast_ratio("#000000", "#FFFFFF"), 1) == 21.0
