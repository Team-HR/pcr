#!/bin/bash
# Use BuildKit with host network to bypass IPv6 issues

export DOCKER_BUILDKIT=1
export BUILDKIT_PROGRESS=plain

echo "Building with host network..."
docker build --network=host -t spms-online:2 .

if [ $? -eq 0 ]; then
    echo "Build successful! Starting containers..."
    docker compose up -d --no-build
else
    echo "Build failed"
fi
