export PROJECT_NAME ?="Circuit breaker"
export RELEASE_FILES ?="Resources/doc/index.md"
export RELEASE_REMOTE ?=origin
export RELEASE_VERSION=$(shell echo ${CURRENT_TAG}|sed -r s/v//)

#   make tag RELEASE_VERSION=(major|minor|patch) RELEASE_FILES="Resources/doc/index.md"
tag: ## Publish new release. Usage: (You need to install https://github.com/flazz/semver/ before)
tag:
	@semver inc $(RELEASE_VERSION)
	@echo "New release: `semver tag`"
	@echo Releasing sources
	@(sed -i -r "s/(v[0-9]+\.[0-9]+\.[0-9]+)/`semver tag`/g" $(RELEASE_FILES)) || true

#   make release
release: ## Tag git with last release
release:
	@(git add .) || true
	@(git commit --no-verify -m "#000 releasing `semver tag`") || true
	@(git tag --delete `semver tag`) || true
	@(git push --no-verify --delete ${RELEASE_REMOTE} `semver tag`) || true
	@git tag `semver tag`
	@git push --no-verify -u ${RELEASE_REMOTE} `semver tag`
	@GIT_CB=$(git symbolic-ref --short HEAD) && git push --no-verify --set-upstream ${RELEASE_REMOTE} $(GIT_CB)

changelog: ## Create CHANGELOG file
changelog:
	@echo " ${PROJECT_NAME} project ($(RELEASE_VERSION)) unstable; urgency=low" >/tmp/changelog
	@echo >> CHANGELOG.md
	@git log $(CURRENT_TAG)...HEAD --pretty=format:'   * %s ' --reverse >> /tmp/changelog
	@echo >> /tmp/changelog
	@echo >> /tmp/changelog
	@echo  "  -- Etienne de Longeaux <sfynx@pi-groupe.net>  $(shell date --rfc-2822)" >> /tmp/changelog
	@echo >> /tmp/changelog
	@# prepend to changelog
	@cat /tmp/changelog|cat - CHANGELOG.md > /tmp/out && mv /tmp/out CHANGELOG.md
	@echo >> CHANGELOG.md
