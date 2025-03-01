#!/usr/bin/env bats

setup() {
	load '../test_helper/common-setup'
	_common_setup
}

@test 'prepares a basic commit message' {
	run -0 "$PROJECT_ROOT/tests/functional/expect/prepare-basic.exp"

	assert_output -p 'feat: this is a test'
}

@test 'prints errors for invalid inputs' {
	skip "Test hangs in CI environment"
	run -0 "$PROJECT_ROOT/tests/functional/expect/prepare-with-alt-config-and-many-errors.exp"

	assert_output -p 'foo(baz): A short description.'

	assert_output -p 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed eget'
	assert_output -p 'ullamcorper velit. Mauris ornare vulputate leo. Ut tempus molestie'
	assert_output -p 'egestas. Donec metus ligula, gravida sit amet eros nec, sollicitudin'
	assert_output -p 'tristique eros. Nunc vestibulum lacinia mi, quis cursus orci tempor in.'

	assert_output -p 'Aliquam felis ex, pretium at elit dapibus, vehicula congue justo.'
	assert_output -p 'Maecenas nulla dui, dignissim ac blandit sed, vulputate in tortor. Fusce'
	assert_output -p 'sed libero eros. Aliquam velit metus, pellentesque non enim sit amet,'
	assert_output -p 'fermentum blandit tellus. Donec nibh augue, feugiat at cursus a,'
	assert_output -p 'convallis id massa. Sed egestas leo a vulputate rhoncus.'

	assert_output -p 'Signed-off-by: Jane Doe <jane@example.com>'
	assert_output -p 'See-also: https://example.com/foo'
}

@test 'prints error for invalid type' {
	run -0 "$PROJECT_ROOT/tests/functional/expect/prepare-with-invalid-type.exp"

	assert_output -p 'feat: this is a test'
}

@test 'prepares a commit message with a full example' {
	skip "Test hangs in CI environment"
	run -0 "$PROJECT_ROOT/tests/functional/expect/prepare-with-full-example.exp"

	assert_output -p 'fix(config)!: use the correct config value'

	assert_output -p 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed eget ullamcorper velit. Mauris ornare vulputate leo. Ut'
	assert_output -p 'tempus molestie egestas. Donec metus ligula, gravida sit amet eros nec, sollicitudin tristique eros. Nunc vestibulum'
	assert_output -p 'lacinia mi, quis cursus orci tempor in.'

	assert_output -p 'Aliquam felis ex, pretium at elit dapibus, vehicula congue justo. Maecenas nulla dui, dignissim ac blandit sed,'
	assert_output -p 'vulputate in tortor. Fusce sed libero eros. Aliquam velit metus, pellentesque non enim sit amet, fermentum blandit'
	assert_output -p 'tellus. Donec nibh augue, feugiat at cursus a, convallis id massa. Sed egestas leo a vulputate rhoncus.'

	assert_output -p 'Cras eu egestas odio. Etiam non urna quam. Mauris nulla orci, placerat in malesuada nec, sagittis id enim. Sed viverra'
	assert_output -p 'accumsan ligula in imperdiet. Aenean pretium faucibus erat non molestie. Mauris sed nibh mauris. Nunc ut fermentum'
	assert_output -p 'felis.'

	assert_output -p 'BREAKING CHANGE: Lorem ipsum dolor sit amet'
	assert_output -p 'fix #4001'
	assert_output -p 're #5001'
	assert_output -p 'see-also: https://example.com/foo'
}
