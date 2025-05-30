const express = require('express');
const http = require('http');
const socketIo = require('socket.io');

const app = express();
const server = http.createServer(app);
const io = socketIo(server);

const PORT = process.env.PORT || 3000;

// Serve static files from the public directory
app.use(express.static('public'));

// Store connected users
const users = {};

// Handle socket connections
io.on('connection', (socket) => {
    console.log('A user connected:', socket.id);

    // Register user
    socket.on('register', (role) => {
        users[socket.id] = { role };
        console.log(`${role} registered with ID: ${socket.id}`);
    });

    // Handle call requests
    socket.on('call-admin', ({ offer, userId }) => {
        socket.to(userId).emit('incoming-call', { offer, userId: socket.id });
    });

    socket.on('call-user', ({ offer, userId }) => {
        socket.to(userId).emit('incoming-call', { offer, userId: socket.id });
    });

    // Handle answer
    socket.on('answer-user', ({ answer, userId }) => {
        socket.to(userId).emit('call-answered', { answer, userId: socket.id });
    });

    // Handle ICE candidates
    socket.on('ice-candidate', ({ candidate, to }) => {
        socket.to(to).emit('ice-candidate', { candidate });
    });

    // Handle hang-up
    socket.on('hang-up', ({ to }) => {
        socket.to(to).emit('call-ended');
    });

    // Handle disconnection
    socket.on('disconnect', () => {
        console.log('User  disconnected:', socket.id);
        delete users[socket.id];
    });
});

// Start the server
server.listen(PORT, () => {
    console.log(`Server is running on http://localhost:${PORT}`);
});
