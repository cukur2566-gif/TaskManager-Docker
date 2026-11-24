# ---------- Stage 1: build static Go binary ----------
FROM golang:1.27-alpine AS builder
WORKDIR /app
COPY go.mod ./
RUN go mod download
COPY main.go ./
RUN CGO_ENABLED=0 GOOS=linux go build -ldflags="-s -w" -o go-backend .

# ---------- Stage 2: minimal runtime ----------
FROM alpine:3.22
WORKDIR /app
COPY --from=builder /app/go-backend .
EXPOSE 8080
HEALTHCHECK --interval=10s --timeout=5s --start-period=5s --retries=3 \
    CMD wget -q -O /dev/null http://127.0.0.1:8080/health || exit 1
CMD ["./go-backend"]
