#!/usr/bin/env bats

setup() {
	load '../test_helper/common-setup'
	_common_setup
}

@test 'dumps JSON config when using a config file with all defaults set' {
	expected_output=$(
		cat <<- 'EOF'
		{
		    "typeCase": null,
		    "types": [],
		    "scopeCase": null,
		    "scopeRequired": false,
		    "scopes": [],
		    "descriptionCase": null,
		    "descriptionEndMark": null,
		    "bodyRequired": false,
		    "bodyWrapWidth": null,
		    "requiredFooters": []
		}
		EOF
	)

	run -0 conventional-commits \
		--no-ansi \
		config \
		--config "$PROJECT_ROOT/tests/configs/default.json" \
		--dump

	assert_output "$expected_output"
}

@test 'dumps JSON config when using config file with typeCase and defaults set' {
	expected_output=$(
		cat <<- 'EOF'
		{
		    "typeCase": "lower",
		    "types": [],
		    "scopeCase": null,
		    "scopeRequired": false,
		    "scopes": [],
		    "descriptionCase": null,
		    "descriptionEndMark": null,
		    "bodyRequired": false,
		    "bodyWrapWidth": null,
		    "requiredFooters": []
		}
		EOF
	)

	run -0 conventional-commits \
		--no-ansi \
		config \
		--config "$PROJECT_ROOT/tests/configs/config-01.json" \
		--dump

	assert_output "$expected_output"
}

@test 'dumps JSON config when using config file with an empty config object' {
	expected_output=$(
		cat <<- 'EOF'
		{
		    "typeCase": null,
		    "types": [],
		    "scopeCase": null,
		    "scopeRequired": false,
		    "scopes": [],
		    "descriptionCase": null,
		    "descriptionEndMark": null,
		    "bodyRequired": false,
		    "bodyWrapWidth": null,
		    "requiredFooters": []
		}
		EOF
	)

	run -0 conventional-commits \
		--no-ansi \
		config \
		--config "$PROJECT_ROOT/tests/configs/config-02.json" \
		--dump

	assert_output "$expected_output"
}

@test 'dumps JSON config when using config file with more properties filled' {
	expected_output=$(
		cat <<- 'EOF'
		{
		    "typeCase": "lower",
		    "types": [
		        "foo",
		        "bar"
		    ],
		    "scopeCase": "snake",
		    "scopeRequired": true,
		    "scopes": [
		        "baz",
		        "qux"
		    ],
		    "descriptionCase": "sentence",
		    "descriptionEndMark": ".",
		    "bodyRequired": true,
		    "bodyWrapWidth": 72,
		    "requiredFooters": [
		        "See-also",
		        "Signed-off-by"
		    ]
		}
		EOF
	)

	run -0 conventional-commits \
		--no-ansi \
		config \
		--config "$PROJECT_ROOT/tests/configs/config-03.json" \
		--dump

	assert_output "$expected_output"
}

@test 'prints error when config file contains invalid config' {
	run ! conventional-commits \
		--no-ansi \
		config \
		--config "$PROJECT_ROOT/tests/configs/config-04.json" \
		--dump

	assert_output --partial 'Expected a configuration array'
	assert_output --partial 'received string instead'
}

@test 'prints error when config file contains invalid type' {
	run ! conventional-commits \
		--no-ansi \
		config \
		--config "$PROJECT_ROOT/tests/configs/config-05.json" \
		--dump

	assert_output --partial 'Invalid /types value found in configuration'
}
