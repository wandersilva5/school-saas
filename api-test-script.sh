#!/bin/bash
# API Testing Script

# Base URL for API
BASE_URL="http://localhost"

# Colors for output
GREEN='\033[0;32m'
RED='\033[0;31m'
YELLOW='\033[0;33m'
NC='\033[0m' # No Color

# Authentication token
TOKEN=""

# Function to make API requests
function call_api {
    method=$1
    endpoint=$2
    data=$3
    auth=$4
    
    echo -e "${YELLOW}Testing: $method $endpoint${NC}"
    
    headers=""
    if [ "$auth" = "true" ] && [ ! -z "$TOKEN" ]; then
        headers="-H \"Authorization: Bearer $TOKEN\""
    fi

    if [ "$method" = "POST" ] || [ "$method" = "PUT" ]; then
        if [ ! -z "$data" ]; then
            cmd="curl -s -X $method \"$BASE_URL$endpoint\" -H \"Content-Type: application/json\" $headers -d '$data'"
        else
            cmd="curl -s -X $method \"$BASE_URL$endpoint\" -H \"Content-Type: application/json\" $headers"
        fi
    else
        cmd="curl -s -X $method \"$BASE_URL$endpoint\" $headers"
    fi
    
    echo "Command: $cmd"
    response=$(eval $cmd)
    
    # Check for errors
    if [[ $response == *"error"* ]]; then
        echo -e "${RED}Failed:${NC} $response\n"
        return 1
    else
        echo -e "${GREEN}Success:${NC} $response\n"
        return 0
    fi
}

# Test login
echo -e "${YELLOW}===== Testing Authentication =====${NC}"
login_response=$(curl -s -X POST "$BASE_URL/api/auth/login" \
    -H "Content-Type: application/json" \
    -d '{"email":"admin@email.com","password":"123456"}')

if [[ $login_response == *"token"* ]]; then
    echo -e "${GREEN}Login successful${NC}"
    # Extract token from response
    TOKEN=$(echo $login_response | sed -n 's/.*"token":"\([^"]*\)".*/\1/p')
    echo "Token obtained: ${TOKEN:0:20}..."
else
    echo -e "${RED}Login failed${NC}: $login_response"
    exit 1
fi

# Test basic endpoints
echo -e "\n${YELLOW}===== Testing Basic Endpoints =====${NC}"

# Dashboard
call_api "GET" "/api/dashboard" "" "true"

# Students
echo -e "\n${YELLOW}===== Testing Student Endpoints =====${NC}"
call_api "GET" "/api/students" "" "true"
call_api "GET" "/api/students/1" "" "true"

# Classes
echo -e "\n${YELLOW}===== Testing Class Endpoints =====${NC}"
call_api "GET" "/api/classes" "" "true"
call_api "GET" "/api/classes/1" "" "true"
call_api "GET" "/api/classes/1/students" "" "true"

# Courses
echo -e "\n${YELLOW}===== Testing Course Endpoints =====${NC}"
call_api "GET" "/api/courses" "" "true"
call_api "GET" "/api/courses/1" "" "true"

# Calendar
echo -e "\n${YELLOW}===== Testing Calendar Endpoints =====${NC}"
call_api "GET" "/api/calendar/events" "" "true"
call_api "GET" "/api/calendar/events/2025-04-01" "" "true"

# Test logout
echo -e "\n${YELLOW}===== Testing Logout =====${NC}"
call_api "POST" "/api/auth/logout" "" "true"

echo -e "\n${GREEN}Testing complete!${NC}"