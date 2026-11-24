package main

import (
	"encoding/json"
	"log"
	"net/http"
	"strconv"
	"strings"
)

type Task struct {
	ID    int    `json:"id"`
	Title string `json:"title"`
}

var tasks = []Task{}
var nextID = 1

func tasksHandler(w http.ResponseWriter, r *http.Request) {
	w.Header().Set("Content-Type", "application/json")

	switch r.Method {
	case http.MethodGet:
		json.NewEncoder(w).Encode(tasks)

	case http.MethodPost:
		var task Task

		err := json.NewDecoder(r.Body).Decode(&task)

		if err != nil || strings.TrimSpace(task.Title) == "" {
			http.Error(w, "Invalid task data", http.StatusBadRequest)
			return
		}

		task.ID = nextID
		nextID++

		tasks = append(tasks, task)

		w.WriteHeader(http.StatusCreated)
		json.NewEncoder(w).Encode(task)

	default:
		w.WriteHeader(http.StatusMethodNotAllowed)
	}
}

func deleteTaskHandler(w http.ResponseWriter, r *http.Request) {
	w.Header().Set("Content-Type", "application/json")

	if r.Method != http.MethodDelete {
		w.WriteHeader(http.StatusMethodNotAllowed)
		return
	}

	idText := strings.TrimPrefix(r.URL.Path, "/tasks/")

	id, err := strconv.Atoi(idText)

	if err != nil {
		http.Error(w, "Invalid task ID", http.StatusBadRequest)
		return
	}

	for i, task := range tasks {
		if task.ID == id {
			tasks = append(tasks[:i], tasks[i+1:]...)

			w.WriteHeader(http.StatusOK)

			json.NewEncoder(w).Encode(map[string]string{
				"message": "Task deleted successfully",
			})

			return
		}
	}

	http.Error(w, "Task not found", http.StatusNotFound)
}

func healthHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodGet {
		http.Error(w, "Method not allowed", http.StatusMethodNotAllowed)
		return
	}

	w.WriteHeader(http.StatusOK)
	w.Write([]byte("OK"))
}

func main() {
	http.HandleFunc("/tasks", tasksHandler)
	http.HandleFunc("/tasks/", deleteTaskHandler)
	http.HandleFunc("/health", healthHandler)

	log.Println("Go Backend running on http://localhost:8080")
	log.Fatal(http.ListenAndServe(":8080", nil))
}
