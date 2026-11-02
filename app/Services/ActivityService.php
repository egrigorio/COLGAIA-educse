<?php

namespace Educse\Services;

use Educse\Exceptions\HttpException;
use Educse\Http\Input;
use Educse\Policies\SchoolPolicy;
use Educse\Repositories\SchoolRepository;

final class ActivityService
{
    public function __construct(private SchoolRepository $db, private SchoolPolicy $policy) {}

    public function save(Input $input, bool $editing): int
    {
        return $this->db->transaction(function () use ($input, $editing) {
            $classId = $input->int('id_turma');
            $subject = $input->int('disciplina');
            $class = $this->policy->teach($classId, $subject);
            $start = $input->date('comeco');
            $end = $input->date('fim');
            if ($end < $start || $start < new \DateTimeImmutable('today') || $start->diff($end)->days > 366) {
                throw new HttpException('As datas devem estar ordenadas, no futuro e dentro de um ano.');
            }
            $effort = $this->db->one('esforco', ['id' => $class['id_esforco']]);
            if (!$effort) { throw new HttpException('Configure o esforço da turma antes de criar atividades.'); }
            if (WorkloadService::workingDays($start, $end, $effort) === 0) { throw new HttpException('O intervalo contém apenas dias de descanso.'); }
            $turn = $input->has('turno') && $input->string('turno', 20, false) !== 'all' ? $input->int('turno', -1) : -1;
            if ($turn !== -1 && !in_array($turn, $this->db->turnIds($classId), true)) { throw new HttpException('Turno não pertence à turma.'); }
            $event = ['titulo' => $input->string('titulo'), 'comeco' => $start->format('Y-m-d'), 'fim' => $end->format('Y-m-d')];
            $activity = ['descricao' => $input->string('descricao', 10000), 'tipo' => $input->string('tipo', 100), 'tempo_sugerido' => $input->number('tempo_sugerido', 0.01), 'id_disciplina' => $subject, 'id_turno' => $turn];
            if ($editing) {
                $eventId = $input->int('id_evento');
                $existing = $this->owned($eventId, $classId);
                $this->db->update('eventos', $event, ['id' => $eventId]);
                $this->db->update('atividades', $activity, ['id' => $existing['id']]);
                return (int) $existing['id'];
            }
            $eventId = $this->db->insert('eventos', $event);
            $id = $this->db->insert('atividades', $activity + ['id_evento' => $eventId, 'id_professor' => $this->policy->id()]);
            $this->db->insert('rel_atividades_turma', ['id_atividade' => $id, 'id_turma' => $classId]);
            return $id;
        });
    }

    public function owned(int $event, ?int $class = null): array
    {
        $activity = $this->db->one('atividades', ['id_evento' => $event, 'id_professor' => $this->policy->id()]);
        if (!$activity || ($class !== null && !$this->db->one('rel_atividades_turma', ['id_atividade' => $activity['id'], 'id_turma' => $class]))) {
            throw new HttpException('Atividade não autorizada', 403);
        }
        return $activity;
    }

    public function remove(int $event): void
    {
        $this->db->transaction(function () use ($event) {
            $this->policy->actor();
            $activity = $this->owned($event);
            $this->db->delete('rel_atividades_turma', ['id_atividade' => $activity['id']]);
            $this->db->delete('atividades', ['id' => $activity['id']]);
            if (!$this->db->one('atividades', ['id_evento' => $event])) { $this->db->delete('eventos', ['id' => $event]); }
        });
    }

    public function effort(Input $input): void
    {
        $this->db->transaction(function () use ($input) {
            $class = $this->policy->classroom($input->int('id_turma'), true);
            if ($input->int('id_esforco') !== (int) $class['id_esforco']) { throw new HttpException('Configuração não pertence à turma', 403); }
            $values = ['limite' => $input->number('limite', 0.01, 24), 'barreira' => $input->number('barreira', 0, 24)];
            if ($values['barreira'] > $values['limite']) { throw new HttpException('O alerta não pode ultrapassar o limite.'); }
            for ($i = 0; $i < 7; $i++) { $values['dia_' . $i] = $input->has('dia_' . $i) ? 1 : 0; }
            $this->db->update('esforco', $values, ['id' => $class['id_esforco']]);
        });
    }

    public function addPersonalEvents(string $json): void
    {
        if ($this->policy->actor()['cargo'] !== 'aluno') { throw new HttpException('Calendário reservado a alunos', 403); }
        try { $events = json_decode($json, true, 32, JSON_THROW_ON_ERROR); }
        catch (\JsonException) { throw new HttpException('JSON de eventos inválido'); }
        if (!is_array($events) || !array_is_list($events) || count($events) > 100) { throw new HttpException('Envie uma lista com até 100 eventos.'); }
        $rows = [];
        foreach ($events as $event) {
            if (!is_array($event)) { throw new HttpException('Evento inválido'); }
            $input = new Input($event);
            $dates = [];
            foreach (['start', 'end'] as $field) {
                $value = $input->string($field, 19);
                $date = \DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:s', $value);
                if (!$date || $date->format('Y-m-d\TH:i:s') !== $value) { throw new HttpException('Data de evento inválida'); }
                $dates[$field] = $date;
            }
            if ($dates['start'] < new \DateTimeImmutable('today') || $dates['end'] <= $dates['start'] || $dates['start']->diff($dates['end'])->days > 366) { throw new HttpException('Intervalo de evento inválido'); }
            $color = $input->string('backgroundColor', 40);
            if (!preg_match('/^#[a-f0-9]{6}$/i', $color)) { throw new HttpException('Cor inválida'); }
            $rows[] = ['id_user' => $this->policy->id(), 'start' => $dates['start']->format('Y-m-d H:i:s'), 'end' => $dates['end']->format('Y-m-d H:i:s'), 'title' => $input->string('title'), 'description' => $input->string('description', 10000, false), 'backgroundColor' => $color];
        }
        $this->db->transaction(function () use ($rows) { foreach ($rows as $row) { $this->db->insert('eventos_alunos_calendario', $row); } });
    }
}
