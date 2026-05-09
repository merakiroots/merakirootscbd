const http = require("http");

const PORT = process.env.PORT || 3000;
const HOST = process.env.HOST || "0.0.0.0";

/**
 * Simple HTTP request handler.
 * @param {http.IncomingMessage} req
 * @param {http.ServerResponse} res
 */
function handleRequest(req, res) {
    // Only respond to GET requests for the root path
    if (req.method === "GET" && req.url === "/") {
        res.writeHead(200, { "Content-Type": "text/plain; charset=utf-8" });
        res.end("Hello, World!\n");
        return;
    }

    // 404 for everything else
    res.writeHead(404, { "Content-Type": "text/plain; charset=utf-8" });
    res.end("Not Found\n");
}

const server = http.createServer(handleRequest);

server.listen(PORT, HOST, () => {
    console.log(`Server listening on http://${HOST}:${PORT}`);
});

// Graceful shutdown handling
function shutdown(signal) {
    console.log(`\nReceived ${signal}. Shutting down gracefully...`);
    server.close(() => {
        console.log("Server closed.");
        process.exit(0);
    });

    // Force exit after 10 seconds if connections are still open
    setTimeout(() => {
        console.error("Forced shutdown after timeout.");
        process.exit(1);
    }, 10000);
}

process.on("SIGTERM", () => shutdown("SIGTERM"));
process.on("SIGINT", () => shutdown("SIGINT"));

// Error handling
process.on("unhandledRejection", (reason, promise) => {
    console.error("Unhandled Rejection at:", promise, "reason:", reason);
});

process.on("uncaughtException", (err) => {
    console.error("Uncaught Exception:", err);
    // It's generally recommended to exit after uncaught exceptions
    // because the application state may be corrupted
    server.close(() => {
        process.exit(1);
    });
});

module.exports = server;
